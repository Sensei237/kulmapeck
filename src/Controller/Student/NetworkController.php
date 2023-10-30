<?php

namespace App\Controller\Student;

use App\Entity\Retrait;
use App\Form\RetraitType;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\RetraitRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NetworkController extends AbstractController
 {

    public function __construct( private Keys $keys )
   {

    }

    #[ Route( '/student/network', name: 'app_student_network' ) ]

    public function index( Request $request, EleveRepository $eleveRepository, PaginatorInterface $paginatorInterface ): Response
 {
        $eleve = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( $eleve === null ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render( 'student/network/index.html.twig', [
            'isNetWork' => true,
            'student' => $eleve,
            'network' => $paginatorInterface->paginate( $eleve->getUtilisateur()->getPersonne()->getInvites(), $request->query->getInt( 'page', 1 ), 10 ),

        ] );
    }

    #[ Route( '/student/retrait/new', name: 'app_student_network_retrait', methods: [ 'POST', 'GET' ] ) ]

    public function retirerEleve( Request $request, EleveRepository $eleveRepository,
    RetraitRepository $retraitRepository, NetworkConfigRepository $networkConfigRepository,
    UserRepository $userRepository ): Response
 {
        $enseignant = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );
        if ( !$enseignant ) {
            throw $this->createAccessDeniedException('NOT CONNECTED');
        }

        $retrait = new Retrait();
        $form = $this->createForm( RetraitType::class, $retrait );
        $form->handleRequest( $request );

        $networkConfig = $networkConfigRepository->findOneBy( [] );
        if ( $networkConfig === null ) {
            throw $this->createAccessDeniedException( "Impossible d'effectuer le retrait pour le moment" );
        }

        if ( $form->isSubmitted() && $form->isValid() ) {
            $reference = 'RT-' . ( time() + rand( 1000, 1000000000000 ) );
            $response = ManageNetwork::convertInMoney( $enseignant->getUtilisateur(), $retrait->getMontant(),
            $retrait->getNumeroTelephone(), $networkConfig, $userRepository, $this->keys, $reference );
            // dd( $response );
            if ( !$response[ 'hasDone' ] ) {
                $this->addFlash( 'success', $response[ 'message' ] );

                return $this->redirectToRoute( 'app_student_network' );
            }

            $retrait->setUser( $enseignant->getUtilisateur() )
            ->setTransactionReference( $response[ 'responseData' ][ 'transaction_ref' ] )
            ->setStatus( $response[ 'responseData' ][ 'status' ] );
            $retraitRepository->save( $retrait, true );

            $this->addFlash( 'success', $response[ 'message' ] );

            return $this->redirectToRoute( 'app_student_network' );
        }

        return $this->render( 'student/network/retrait.html.twig', [
            'isNetwork' => true,
            'student' => $enseignant,
            'enseignant' => $enseignant,
            'form' => $form->createView(),
            'networkConfig' => $networkConfig,
        ] );
    }

    #[Route('/student/retraits', name: 'app_student_network_retraits', methods: ['GET'])]
    public function retraits(EleveRepository $eleveRepository, NetworkConfigRepository $networkConfigRepository): Response
    {
        $enseignant = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }

        $networkConfig = $networkConfigRepository->findOneBy([]);
        
        $retraits = $enseignant->getUtilisateur()->getRetraits();
        $montantTotal = 0;
        foreach ($retraits as $retrait) {
            $montantTotal += $retrait->getMontant();
        }

        return $this->render('student/network/retrait.html.twig', [
            'isNetwork' => true,
            'enseignant' => $enseignant,
            'networkConfig' => $networkConfig,
            'retraits' => $retraits,
            'student' => $enseignant,
            'showHistorique' => true,
            'montantTotal' => $montantTotal,
        ]);
    }

}
