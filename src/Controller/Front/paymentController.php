<?php

namespace App\Controller\Front;

use App\Entity\Cours;
use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class paymentController extends AbstractController
{
    #[Route('/', name: 'app_front_payment')]
    public function index(): Response
    {
        return $this->render('front/payment/index.html.twig', [
            'controller_name' => 'paymentController',
        ]);
    }

    #[Route('/course/{slug}/buy', name: 'app_front_payment_buy_course', methods: ['GET', 'POST'])]
    public function devenirPremiumOrByCourse(Cours $course, Request $request, EleveRepository $eleveRepository)
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
                dd("En attente des API...");
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
}
