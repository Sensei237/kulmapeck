<?php

namespace App\Controller\Student;

use App\Repository\EleveRepository;
use App\Repository\PaymentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/student/payment-list', name: 'app_student_payments')]
    public function index(EleveRepository $eleveRepository, PaymentRepository $paymentRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        $payments = $paymentRepository->findBy(['eleve' => $eleve], ['paidAt' => 'DESC']);

        return $this->render('student/payment/index.html.twig', [
            'controller_name' => 'PaymentController',
            'isPayments' => true,
            'student' => $eleve,
            'payments' => $paginatorInterface->paginate($payments, $request->query->getInt('page', 1), 10),
            
        ]);
    }

}
