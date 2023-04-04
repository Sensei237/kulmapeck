<?php

namespace App\Controller\Front;

use App\Repository\CategorieRepository;
use App\Repository\ClasseRepository;
use App\Repository\ExamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('')]
class ExamController extends AbstractController
{
    #[Route('/exams', name: 'app_front_exam_index')]
    public function index(Request $request, CategorieRepository $categorieRepository, ClasseRepository $classeRepository, ExamRepository $examRepository, PaginatorInterface $paginatorInterface): Response
    {
        $exams = $examRepository->findBy([]);

        return $this->render('front/exam/index.html.twig', [
            'controller_name' => 'ExamController',
            'isExamPage' => true,
            'exams' => $paginatorInterface->paginate($exams, $request->query->getInt('page', 1), 10),
            'categories' => $categorieRepository->findAll(),
            'classes' => $classeRepository->findAll(),
            
        ]);
    }
}
