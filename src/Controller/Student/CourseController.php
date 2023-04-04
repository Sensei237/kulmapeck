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

class CourseController extends AbstractController
{

    #[Route('/student/courses', name: 'app_student_courses')]
    public function index(Request $request, EleveRepository $eleveRepository, PaginatorInterface $paginatorInterface, CoursRepository $coursRepository): Response
    {
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        $courses = $paginatorInterface->paginate($coursRepository->findForStudent($eleve, $request->query->get('search')), $request->query->getInt('page', 1), 5);

        return $this->render('student/course/index.html.twig', [
            'controller_name' => 'HomeController',
            'studentCourses' => true,
            'courses' => $courses,
            'student' => $eleve,
        ]);
    }

    #[Route('/student/{reference}/{slug}/remove-course', name: 'app_student_payments')]
    public function removeCourse(Eleve $eleve, Cours $cours, EleveRepository $eleveRepository): Response
    {
        $e = $eleveRepository->findOneBy(['utilisateur' => $this->getUser()]);

        if ($eleve !== $e) {
            throw $this->createAccessDeniedException();
        }

        $eleve->removeCour($cours);
        $eleveRepository->save($eleve, true);

        return $this->redirectToRoute('app_student_courses');
    }
}
