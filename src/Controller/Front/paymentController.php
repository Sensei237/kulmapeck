<?php

namespace App\Controller\Front;

use App\Entity\Abonnement;
use App\Entity\Cours;
use App\Entity\Payment;
use App\Entity\PaymentMethod;
use App\Repository\AbonnementItemRepository;
use App\Repository\EleveRepository;
use App\Repository\NetworkConfigRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;
use App\Utils\Keys;
use App\Utils\ManageNetwork;
use Doctrine\ORM\EntityManagerInterface;
use PaymentUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
   

    #[Route('/', name: 'app_front_payment')]
    public function index(): Response
    {
        return $this->render('front/payment/index.html.twig', [
            'controller_name' => 'paymentController',
        ]);
    }

    #[Route('/course/{slug}/buy', name: 'app_front_payment_buy_course', methods: ['GET', 'POST'])]
    public function devenirPremiumOrByCourse(Cours $course, Request $request, UserRepository $userRepository, NetworkConfigRepository $networkConfigRepository, PaymentMethodRepository $paymentMethodRepository, PaymentRepository $paymentRepository, EleveRepository $eleveRepository, EntityManagerInterface $em)
    {
        // La fonction nécessite que l'on soit connecté et surtout qu'on soit élève
        $this->denyAccessUnlessGranted('ROLE_STUDENT');

        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($eleve === null) {
            throw $this->createAccessDeniedException();
        }

        if ($request->request->get('initiate_payment')) {
            if ($this->isCsrfTokenValid('payment' . $course->getId(), $request->request->get('_token'))) {
                // En fonction de la methode de payment choisie on fait appel à l'API indiquée
                $paymentMethod = $paymentMethodRepository->findOneBy(['code' => $request->request->get('payment_method')]);
                $reference = 'CO-' . (time() + rand(10000, 100000000000));
                $apiResponse = PaymentUtil::initierPayment($eleve->getUtilisateur(), $course, $paymentMethod, $this->keys, $reference);
                // dd($apiResponse);
                if ($apiResponse['isPaied'] && isset($apiResponse['responseData']['payment_url']) && isset($apiResponse['responseData']['transaction_ref']) && isset($apiResponse['responseData']['status'])) {
                    $eleve->addCour($course);
                    $payment = new Payment();
                    $payment->setEleve($eleve)
                        ->setPaymentMethod($paymentMethod)
                        ->setCours($course)
                        ->setPaidAt(new \DateTimeImmutable())
                        ->setIsExpired(true)
                        ->setTransactionReference($apiResponse['responseData']['transaction_ref'])
                        ->setStatus($apiResponse['responseData']['status'])
                        ->setAmount($course->getMontantAbonnement())
                        ->setReference($reference);
                    $paymentRepository->save($payment, true);
                    $this->addFlash('success', "Votre paiement a été initié !");

                    return $this->redirect($apiResponse['responseData']['payment_url']);

                    $networkConfigs = $networkConfigRepository->findAll();
                    if (!empty($networkConfigs)) {
                        ManageNetwork::manage($eleve->getUtilisateur(), $networkConfigs[0], $userRepository, $em);
                    }

                    return $this->redirectToRoute('app_front_course_details', ['slug' => $course->getSlug()]);
                }

                throw $this->createAccessDeniedException("Impossible d'effectuer le paiement !");
            }
            else {
                throw $this->createAccessDeniedException("Operation impossible");
            }
            
        }
        
        return $this->render('front/payment/buy_course.html.twig', [
            'isCoursePage' => true,
            'course' => $course,
            'student' => $eleve,
            'paymentMethods' => $course->getPaymentMethods(),
        ]);
    }

    #[Route('/abonnement/{slug}/subscribe', name: 'app_front_payment_buy_plan', methods: ['GET', 'POST'])]
    public function subscribePlan(Request $request, Abonnement $abonnement, EleveRepository $eleveRepository, PaymentRepository $paymentRepository, PaymentMethodRepository $paymentMethodRepository, AbonnementItemRepository $abonnementItemRepository): Response
    {

        // La fonction nécessite que l'on soit connecté et surtout qu'on soit élève
        $this->denyAccessUnlessGranted('ROLE_STUDENT');

        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($eleve === null) {
            throw $this->createAccessDeniedException();
        }

        if ($request->request->get('initiate_payment')) {
            if ($this->isCsrfTokenValid('payment' . $abonnement->getId(), $request->request->get('_token'))) {
                // En fonction de la methode de payment choisie on fait appel à l'API indiquée
                $paymentMethod = $paymentMethodRepository->findOneBy(['code' => $request->request->get('payment_method')]);
                $reference = 'AB-' . (time() + rand(10000, 100000000000));
                $apiResponse = PaymentUtil::initierPaymentPlan($eleve->getUtilisateur(), $abonnement, $paymentMethod, $this->keys, $reference);
                if ($apiResponse['isPaied'] && isset($apiResponse['responseData']['payment_url']) && isset($apiResponse['responseData']['transaction_ref']) && isset($apiResponse['responseData']['status'])) {
                    
                    $payment = new Payment();
                    $today = date_format(new \DateTimeImmutable(), 'Y-m-d');
                    $expiredAt = strtotime($today . ' +' . $abonnement->getDuree() . ' day');
                    $payment->setEleve($eleve)
                        ->setAbonnement($abonnement)
                        ->setIsExpired(true)
                        ->setPaymentMethod($paymentMethod)
                        ->setReference(time()+$eleve->getId())
                        ->setAmount($abonnement->getMontant())
                        ->setTransactionReference($apiResponse['responseData']['transaction_ref'])
                        ->setStatus($apiResponse['responseData']['status'])
                        ->setExpiredAt(new \DateTimeImmutable(date('Y-m-d H:i:s', $expiredAt)));
                    
                    $paymentRepository->save($payment);

                    $eleve->setIsPremium(false);
                    
                    $eleveRepository->save($eleve, true);
                    $this->addFlash('success', "Votre paiement a été initié !");

                    return $this->redirect($apiResponse['responseData']['payment_url']);

                    // return $this->redirectToRoute('app_home');
                }

                throw $this->createAccessDeniedException("Impossible d'effectuer le paiement !");
            }
            else {
                throw $this->createAccessDeniedException("Operation impossible");
            }
            
        }


        return $this->render('front/payment/subscribe_abonnement.html.twig', [
            'plan' => $abonnement,
            'student' => $eleve,
            'abonnementItems' => $abonnementItemRepository->findAll(),
        ]);
    }

}
