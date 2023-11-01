<?php

namespace App\Controller\Student;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\RetraitRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[ Route( '/api/student/money' ) ]

class MoneyBankController extends AbstractController
 {
    private $entityManager;
    private SerializerInterface $serializer;

    public function __construct( EntityManagerInterface $entityManager
    , private Security $security,
    SerializerInterface $serializer,
    private Keys $keys )
 {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    #[ Route( '/', methods: [ 'GET' ] ) ]

    public function getStudentMoney(
        EleveRepository $eleveRepository,
        NetworkConfigRepository $networkConfigRepository, Request $request
    ): JsonResponse {
        $user = $this->security->getUser();

        if ( !$user ) {
            throw $this->createAccessDeniedException();
        }

        $networkConfig = $networkConfigRepository->findOneBy( [] );

        return $this->json( [
            'student' => json_decode( $this->serializer->serialize( $user, 'json', [ 'groups' => [ 'read:user:item', 'read:retraits:item', 'read:personne:item' ] ] ) ),
            'config' => $networkConfig,
        ], 200 );
    }

    #[ Route( '/retrait', methods: [ 'POST' ] ) ]

    public function retirerArgent( Request $request, EleveRepository $eleveRepository,
    RetraitRepository $retraitRepository, NetworkConfigRepository $networkConfigRepository,
    UserRepository $userRepository ): JsonResponse
 {
        $requestData = json_decode( $request->getContent(), true );
        $amount = $requestData[ 'amount' ];
        $phone = $requestData[ 'phone' ];

        $enseignant = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( !$enseignant ) {
            throw $this->createAccessDeniedException( 'NOT CONNECTED' );
        }

        $retrait = new \App\Entity\Retrait();
        $networkConfig = $networkConfigRepository->findOneBy( [] );
        if ( $networkConfig === null ) {
            throw $this->createAccessDeniedException( "Impossible d'effectuer le retrait pour le moment" );
        }

        $reference = 'RT-' . ( time() + rand( 1000, 1000000000000 ) );
        $response = ManageNetwork::convertInMoney( $enseignant->getUtilisateur(), $amount,
        $phone, $networkConfig, $userRepository, $this->keys, $reference );

        if ( !$response[ 'hasDone' ] ) {

            return $this->json( [ 'success'=> $response[ 'message' ] ], 403 );
        }

        $retrait->setUser( $enseignant->getUtilisateur() )
        ->setTransactionReference( $response[ 'responseData' ][ 'transaction_ref' ] )
        ->setStatus( $response[ 'responseData' ][ 'status' ] );
        $retraitRepository->save( $retrait, true );

        return $this->json( [ 'success'=> $response[ 'message' ] ], 200 );
    }

    #[ Route( '/contact', methods: [ 'POST' ] ) ]

    public function sendSMS( Request $request,
    ContactRepository $contactRepository, MailerInterface $mailer ): JsonResponse
 {
        $requestData = json_decode( $request->getContent(), true );
        $object = $requestData[ 'object' ];
        $content = $requestData[ 'content' ];
        $date = new \DateTimeImmutable();

        $contact = new Contact();
        $contact->setObject( $object )
        ->setContent( $content )->setCreateAt( $date )
        ->setUser( $this->getUser() );
        $result =       $contactRepository->save( $contact, true );

        $recipients = [ 'ondouabenoit392@gmail.com',
        'patmbantio@gmail.com', 'kenbalain@gmail.com'
        , 'bissayaivant@gmail.com' ];

        foreach ( $recipients as $recipient ) {
            $recipient ;

            $email = ( new Email() )
            ->from( 'contact@kulmapeck.com' )
            ->to( $recipient )
            ->subject( $object )
            ->text( $content )
            ->html( '<p>' . $content. '</p>' );
            $mailer->send( $email );

        }

        return $this->json( [ 'success'=> $result ], 200 );
    }

    private function calculatePayoutAmount( $retraits ): float
 {
        $montantTotal = 0;

        foreach ( $retraits as $retrait ) {
            $montantTotal += $retrait->getMontant();
        }

        return $montantTotal;
    }
}
