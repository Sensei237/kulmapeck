<?php

namespace App\Controller\IInstructor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdersController extends AbstractController
{
    #[Route('/i/instructor/orders', name: 'app_i_instructor_orders')]
    public function index(): Response
    {
        return $this->render('i_instructor/orders/index.html.twig', [
            'controller_name' => 'OrdersController',
        ]);
    }
}
