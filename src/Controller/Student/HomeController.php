<?php

namespace App\Controller\Student;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/student/home', name: 'app_student_home')]
    public function index(EleveRepository $eleveRepository): Response
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        return $this->render('student/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'studentHome' => true,
            'student' => $eleve,
        ]);
    }
    
}
