<?php

namespace App\Controller\Api\Controller\Payment;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Payment;
use App\Repository\EleveRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use App\Utils\Keys;
use App\Utils\PaymentUtil;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        private PaymentRepository $paymentRepository,
        private Keys $keys
    ) {
    }

    public function __invoke(Cours $course, Request $request)
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        if ($course->isIsFree()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas payer ce cours car il est gratuit !');
        }

        if (!$course->isIsValidated()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas payer ce cours car il n\'est pas encore publié !');
        }

        $data = $request->toArray();

        if (empty($data['payment_method']) || empty($data['phone'])) {
            throw new BadRequestException("Vous devez préciser la méthode de paiement et le numero de telephone!");
        }

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $data['payment_method']]);

        if ($paymentMethod == null) {
            throw new BadRequestException("La méthode de paiement envoyée n'existe pas !");
        }

        $reference = 'CO-' . (time() + rand(10000, 100000000000));
        $phoneNumber = $data['phone'];
        $apiResponse = PaymentUtil::initierPayment($eleve->getUtilisateur(), $course, $paymentMethod, $this->keys, $reference, $phoneNumber);

        if ($apiResponse['isPaied'] && isset($apiResponse['responseData']['payment_url']) && isset($apiResponse['responseData']['transaction_ref']) && isset($apiResponse['responseData']['status'])) {
            // $eleve->addCour($course);
            $payment = new Payment();
            $payment->setEleve($eleve)
                ->setPaymentMethod($paymentMethod)
                ->setCours($course)->setPaidAt(new \DateTimeImmutable())
                ->setIsExpired(false)
                ->setAmount($course->getMontantAbonnement())
                ->setTransactionReference($apiResponse['responseData']['transaction_ref'])
                ->setStatus('en cours')
                ->setReference($reference);
            $this->paymentRepository->save($payment, true);
        }else {
            throw new BadRequestHttpException("Impossible d'effectuer le paiement");
        }

        return new \ArrayObject([
            'isPaied' => true,
            'message' => 'Votre paiement a été aprouvé !',
            'paiements' => $eleve->getPayments(),
            'apiUrl' => $apiResponse['responseData']['payment_url']
        ]);
    }
}
