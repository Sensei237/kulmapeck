<?php

namespace App\Controller\Student;

use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends AbstractController
{
    #[Route('/student/evaluation', name: 'app_student_evaluation')]
    public function index(EleveRepository $eleveRepository): Response
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        return $this->render('student/evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
            'isEvaluations' => true,
            'student' => $eleve,
        ]);
    }
}
