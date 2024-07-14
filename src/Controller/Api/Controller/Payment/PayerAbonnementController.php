<?php

namespace App\Controller\Api\Controller\Payment;

use App\Entity\Abonnement;
use App\Entity\Payment;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\PaymentUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
        private PaymentRepository $paymentRepository,
        private NetworkConfigRepository $networkConfigRepository,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private Keys $keys
    ) {
    }

    public function __invoke(Abonnement $abonnement, Request $request): \ArrayObject
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        // if ($this->paymentRepository->findOneBy(['isExpired'=>false, 'eleve'=>$eleve]) !== null) {
        //     throw $this->createAccessDeniedException('Vous avez un abonnement actif ! Vous ne pouvez plus souscrire à un nouvel abonnement');
        // }

        $data = $request->toArray();

        if (empty($data['payment_method']) || empty($data['phone'])) {
            throw new BadRequestHttpException("Vous devez préciser la méthode de paiement !");
        }

        $paymentMethod = $this->paymentMethodRepository->findOneBy(['code' => $data['payment_method']]);

        if ($paymentMethod == null) {
            throw new BadRequestHttpException("La méthode de paiement envoyée n'existe pas !");
        }

        if ($paymentMethod == null) {
            throw new BadRequestHttpException("La méthode de paiement envoyée n'existe pas !");
        }
        $reference = 'AB-' . (time() + rand(10000, 100000000000));
        $phoneNumber = $data['phone'];
        $apiResponse = PaymentUtil::initierPaymentPlan($eleve->getUtilisateur(), $abonnement, $paymentMethod, $this->keys, $reference, $phoneNumber);
        if ($apiResponse['isPaied'] && isset($apiResponse['responseData']['payment_url']) && isset($apiResponse['responseData']['transaction_ref']) && isset($apiResponse['responseData']['status'])) {
                    
            $payment = new Payment();
            $today = date_format(new \DateTimeImmutable(), 'Y-m-d H:i:s');
            $expiredAt = strtotime($today . ' +' . $abonnement->getDuree() . ' day');
            $payment->setEleve($eleve)
                ->setAbonnement($abonnement)
                ->setIsExpired(false)
                ->setPaymentMethod($paymentMethod)
                ->setReference($reference)
                ->setTransactionReference($apiResponse['responseData']['transaction_ref'])
                ->setStatus('en cours')
                ->setAmount($abonnement->getMontant())
                ->setExpiredAt(new \DateTimeImmutable(date('Y-m-d H:i:s', $expiredAt)));
            
            $this->paymentRepository->save($payment, true);

            $eleve->setIsPremium(false);
            
            $this->eleveRepository->save($eleve, true);

        }else {
            throw new BadRequestHttpException("Impossible d'initier le payment");
        }
        
        return new \ArrayObject([
            'isPaied' => true,
            'message' => 'Votre paiement a été aprouvé ! Vous êtes désormais premium. Pensez à renouveler votre abonnement avant le ' . date_format($payment->getExpiredAt(), 'dd/mm/yyyy'),
            'paiements' => $eleve->getPayments(),
            'apiUrl' => $apiResponse['responseData']['payment_url']
        ]);
    }
}
