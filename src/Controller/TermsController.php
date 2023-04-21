<?php

namespace App\Controller;

use App\Repository\TermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TermsController extends AbstractController
{
    #[Route('/terms-and-conditions', name: 'app_terms')]
    public function index(TermRepository $termRepository): Response
    {
        return $this->render('terms/index.html.twig', [
            'controller_name' => 'TermsController',
            'terms' => $termRepository->findAll(),
        ]);
    }
}
