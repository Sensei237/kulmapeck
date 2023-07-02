<?php

namespace App\Controller\Api\Controller\Payment;

use App\Entity\Abonnement;
use App\Entity\Eleve;
use App\Entity\Payment;
use Doctrine\Common\Collections\Collection;
use App\Repository\EleveRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use PaymentUtil;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class PayerAbonnementController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private PaymentMethodRepository $paymentMethodRepository,
        private PaymentRepository $paymentRepository
    ) {
    }

    public function __invoke(Abonnement $abonnement, Request $request): Collection
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        if ($request->request->get('initiate_payment')) {
            // En fonction de la methode de payment choisie on fait appel à l'API indiquée
            $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $request->request->get('payment_method')]);
            if (PaymentUtil::initierPaymentPlan($abonnement, $paymentMethod)) {

                $payment = new Payment();
                $today = date_format(new \DateTimeImmutable(), 'Y-m-d');
                $expiredAt = strtotime($today . ' +' . $abonnement->getDuree() . ' day');
                $payment->setEleve($eleve)
                    ->setAbonnement($abonnement)
                    ->setIsExpired(false)
                    ->setPaymentMethod($paymentMethod)
                    ->setReference(time()+$eleve->getId())
                    ->setAmount($abonnement->getMontant())
                    ->setExpiredAt(new \DateTimeImmutable(date('Y-m-d H:i:s', $expiredAt)));
                
                $this->paymentRepository->save($payment);

                $eleve->setIsPremium(true);
                
                $this->eleveRepository->save($eleve, true);
            }else {
                throw new BadRequestHttpException("Impossible d'initier le payment");
            }
        }else {
            throw $this->createAccessDeniedException("Impossible d'effectuer le paiement !");
        }

        return $eleve->getPayments();
    }
}
