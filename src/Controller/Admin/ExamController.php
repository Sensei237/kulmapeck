<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    #[Route('/admin/exam', name: 'app_admin_exam')]
    public function index(): Response
    {
        return $this->render('admin/exam/index.html.twig', [
            'controller_name' => 'ExamController',
        ]);
    }
}
