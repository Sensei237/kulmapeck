<?php

namespace App\Controller\Api\Controller\User;

use App\Entity\User;
use App\Repository\EleveRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[ AsController ]

class MeController extends AbstractController
 {

    public function __construct(
        private Security $security,
        private EleveRepository $eleveRepository,
        private PaymentRepository $paymentRepository
    ) {
    }

    public function __invoke(): ?User
 {
        $user = $this->security->getUser();

        $eleve =$this->eleveRepository->findOneBy( [ 'utilisateur' => $user ] );

        if ( $eleve ) {

            $payments = $this->paymentRepository->findBy( [ 'eleve'=>$eleve, 'status'=>'SUCCESS', 'isExpired'=>false ] );
            $today = new \DateTimeImmutable();
            $hasNoValidAbonnment = true;
            if ( count( $payments )>0 ) {
                foreach ( $payments as $payment ) {
                    if ( $payment->getExpiredAt()>$today ) {
                        $hasNoValidAbonnment = false;
                    } else {
                        $payment->setIsExpired( true );
                        $this->paymentRepository->save( $payment, true );
                    }
                }

            }
            if ( $hasNoValidAbonnment ) {
                $eleve->setIsPremium( false );
                $this->eleveRepository->save( $eleve, true );
            }
        }

        return $user;
    }
}
