<?php

namespace App\Controller\Api\Controller;

use App\Repository\EleveRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends AbstractController
 {
    private $authorizationChecker;

    public function __construct( AuthorizationCheckerInterface $authorizationChecker )
 {
        $this->authorizationChecker = $authorizationChecker;
    }

    #[ Route( '/api/login', name: 'api_login', methods: [ 'POST' ] ) ]

    public function login( EleveRepository $eleveRepository,
    PaymentRepository $paymentRepository )
 {
        $user = $this->getUser();

        $eleve = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );

        if ( $eleve ) {

            $payments = $paymentRepository->findBy( [ 'eleve'=>$eleve, 'status'=>'SUCCESS', 'isExpired'=>false ] );
            $today = new \DateTimeImmutable();
            $hasNoValidAbonnment = true;
            if ( count( $payments )>0 ) {
                foreach ( $payments as $payment ) {
                    if ( $payment->getExpiredAt()>$today ) {
                        $hasNoValidAbonnment = false;
                    } else {
                        $payment->setIsExpired( true );
                        $paymentRepository->save( $payment, true );
                    }
                }

            }
            if ( $hasNoValidAbonnment ) {
                $eleve->setIsPremium( false );
                $eleveRepository->save( $eleve, true );
            }
        }
        if ( null === $user ) {
            return $this->json( [
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED );
        }

        if ( $this->authorizationChecker->isGranted( 'ROLE_STUDENT' ) ) {
            return $this->json( [
                'message' => 'Accès autorisé uniquement aux apprenants',
            ], Response::HTTP_UNAUTHORIZED );
        }

        if ( !in_array( 'ROLE_STUDENT', $user->getRoles() ) ) {
            return $this->json( [
                'message' => 'Accès autorisé uniquement aux apprenants',
            ], Response::HTTP_UNAUTHORIZED );
        }

        return $this->json( [
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ] );
    }
}
