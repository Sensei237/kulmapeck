<?php

namespace App\Controller\Student;

use App\Repository\EleveRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
 {
    #[ Route( '/student/home', name: 'app_student_home' ) ]

    public function index( EleveRepository $eleveRepository,
    PaymentRepository $paymentRepository ): Response
 {
        $eleve = $eleveRepository->findOneBy( [ 'utilisateur' => $this->getUser() ] );

        $payments = $paymentRepository->findBy( [ 'eleve'=>$eleve, 'status'=>'SUCCESS', 'isExpired'=>false ] );
        $today = new \DateTimeImmutable();

        $hasNoValidAbonnment = true;
        if ( count( $payments )>0 ) {
            foreach ( $payments as $payment ) {
                if ( $payment->getExpiredAt()>$today ) {
                    $hasNoValidAbonnment = false;
                } else {
                    $payment->setIsExpired( true );
                    $paymentRepository->save($payment,true);
                }
            }

        }
        if($hasNoValidAbonnment){
            $eleve->setIsPremium(false);
            $eleveRepository->save($eleve,true);
        }

        return $this->render( 'student/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'studentHome' => true,
            'student' => $eleve,
        ] );
    }

}
