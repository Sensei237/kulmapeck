<?php

namespace App\Controller\Api\Controller\Payment;

use App\Entity\Cours;
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
class PayerCoursController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private PaymentMethodRepository $paymentMethodRepository,
        private PaymentRepository $paymentRepository
    ) {
    }

    public function __invoke(Cours $course, Request $request): Collection
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $request->request->get('payment_method')]);
        if (PaymentUtil::initierPayment($course, $paymentMethod)) {
            $eleve->addCour($course);
            $payment = new Payment();
            $payment->setEleve($eleve)
                ->setPaymentMethod($paymentMethod)
                ->setCours($course)->setPaidAt(new \DateTimeImmutable())
                ->setIsExpired(false)
                ->setAmount($course->getMontantAbonnement())
                ->setReference('PAI-' . time() + rand(10000, 100000000000) + $payment->getId());
            $this->paymentRepository->save($payment, true);
        }else {
            throw new BadRequestHttpException("Impossible d'effectuer le paiement");
        }

        return $eleve->getPayments();
    }
}
