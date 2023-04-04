<?php

namespace App\Controller\Instructor;

use App\Repository\EnseignantRepository;
use App\Repository\PaymentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instructor/orders')]
class OrdersController extends AbstractController
{
    #[Route('/', name: 'app_instructor_orders_index')]
    public function index(Request $request, PaginatorInterface $paginatorInterface, PaymentRepository $paymentRepository, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);
        if ($enseignant === null) {
            throw $this->createAccessDeniedException();
        }
        
        $orders = $paymentRepository->findTeacherCoursesPayments($enseignant);
        $nbPerPage = 10;
        $totalSales = 0;
        foreach ($orders as $order) {
            $totalSales += $order->getCours()->getMontantAbonnement();
        }

        // dd($paginatorInterface->paginate($orders, $request->query->getInt('page', 1), $nbPerPage)->getItemNumberPerPage());

        return $this->render('instructor/orders/index.html.twig', [
            'controller_name' => 'OrdersController',
            'orders' => $paginatorInterface->paginate($orders, $request->query->getInt('page', 1), $nbPerPage),
            'isOrders' => true,
            'currentPage' => $request->query->getInt('page', 1),
            'totalItems' => count($orders),
            'nbPerPage' => $nbPerPage,
            'totalSales' => $totalSales,
        ]);
    }
}
