<?php

namespace App\Controller\Instructor;

use App\Repository\CoursRepository;
use App\Repository\EnseignantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/instructor', name: 'app_instructor_home')]
    public function index(Request $request, CoursRepository $coursRepository, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = $enseignantRepository->findOneBy(['utilisateur' => $this->getUser()]);

        return $this->render('instructor/home/index.html.twig', [
            'lastCourses' => $coursRepository->findBy(['enseignant' => $enseignant], ['createdAt' => "DESC"], 5),
            'enseignant' => $enseignant,
            'premiumCourses' => $coursRepository->findBy(['enseignant' => $enseignant, 'isFree' => false]),
            'freeCourses' => $coursRepository->findBy(['enseignant' => $enseignant, 'isFree' => true]),
            'publishedCourses' => $coursRepository->findBy(['enseignant' => $enseignant, 'isPublished' => true, ]),
            'pendingCourses' => $coursRepository->findBy(['enseignant' => $enseignant, 'isPublished' => false,]),
            'instructorHome' => true,
        ]);
    }
}
