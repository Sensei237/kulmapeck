<?php

namespace App\Controller;

use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EnseignantRepository $enseignantRepository, EleveRepository $eleveRepository): Response
    {
        if ($enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]) !== null) {
            return $this->redirectToRoute('app_instructor_home');
        }
        elseif ($eleveRepository->findOneBy(['utilisateur' => $this->getUser()]) !== null) {
            return $this->redirectToRoute('app_student_home');
        }

        return $this->redirectToRoute('app_front');
    }
}
