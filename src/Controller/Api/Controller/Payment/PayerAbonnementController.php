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
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

        $data = $request->toArray();

        if (empty($data['payment_method'])) {
            throw new BadRequestHttpException("Vous devez préciser la méthode de paiement !");
        }

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $data['payment_method']]);

        if ($paymentMethod == null) {
            throw new BadRequestHttpException("La méthode de paiement envoyée n'existe pas !");
        }

        if ($paymentMethod == null) {
            throw new BadRequestHttpException("La méthode de paiement envoyée n'existe pas !");
        }

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
        

        return $eleve->getPayments();
    }
}