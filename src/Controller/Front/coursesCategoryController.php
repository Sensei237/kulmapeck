<?php

namespace App\Controller\Front;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class coursesCategoryController extends AbstractController
{
    #[Route('/courses-categories', name: 'app_front_courses_category')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('front/courses_category/index.html.twig', [
            'categories' => $categorieRepository->findBy(['isSubCategory' => false], ['name' => 'ASC']),
            'isCoursePage' => true,
        ]);
    }
}
