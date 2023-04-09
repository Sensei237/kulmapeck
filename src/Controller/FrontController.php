<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\AbonnementItemRepository;
use App\Repository\AbonnementRepository;
use App\Repository\CategorieRepository;
use App\Repository\CoursRepository;
use App\Repository\EleveRepository;
use App\Repository\FormationRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller contient toutes les methodes utiles pour le fonctionnement du site web
 */

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(Request $request, EleveRepository $eleveRepository, CategorieRepository $categorieRepository, CoursRepository $coursRepository, ReviewRepository $reviewRepository): Response
    {
        $categories = [];
        foreach ($categorieRepository->findBCategories() as $category) {
            $categories[] = [
                'category' => $category,
                'courses' => $coursRepository->findBy(['categorie' => $category, 'isValidated' => true], ['vues' => 'DESC'], 8)
            ];
        }

        return $this->render('front/home/index.html.twig', [
            'controller_name' => 'FrontController',
            'categories' => $categories,
            'trendingCourses' => $coursRepository->findBy(['isValidated' => true], ['isFree' => 'ASC', 'review' => 'DESC', 'createdAt' => 'DESC'], 6),
            'topReviews' => $reviewRepository->findBy([], ['rating' => 'DESC', 'createdAt' => 'DESC'], 2),
            'dailyStudents' => $eleveRepository->findBy([], ['joinAt' => 'DESC'], 4),
            'isHomePage' => true,
        ]);
    }

    #[Route('/about-us', name: 'app_front_about')]
    public function about(): Response 
    {
        return $this->render('front/about/index.html.twig', [
            'isAboutPage' => true,
        ]);
    }

    #[Route('/contact-us', name: 'app_front_contact')]
    public function contact(): Response
    {
        return $this->render('front/contact/index.html.twig', [
            'isContactPage' => true,
        ]);
    }

    #[Route('/header-categories', name: 'app_front_header_categories')]
    public function showHeaderCategories(CategorieRepository $categorieRepository): Response 
    {
        return $this->render('front/home/header/categories.html.twig', [
            'categories' => $categorieRepository->findBy(['isSubCategory' => false], [], 11)
        ]);
    }

    #[Route('/header-courses', name: 'app_front_header_courses_and_formations')]
    public function showHeaderCourses(CategorieRepository $categorieRepository, CoursRepository $coursRepository, FormationRepository $formationRepository): Response
    {
        return $this->render('front/home/header/courses.html.twig', [
            'categories' => $categorieRepository->findBy(['isSubCategory' => false]),
            'lastCourses' => $coursRepository->findBy(['isValidated' => true], ['createdAt' => 'DESC', 'vues' => 'DESC'], 8)
        ]);
    }

    #[Route('/plans', name: 'app_plan')]
    public function plan(Request $request, AbonnementItemRepository $abonnementItemRepository, AbonnementRepository $abonnementRepository) {

        return $this->render('front/plan/index.html.twig', [
            'plans' => $abonnementRepository->findAll(),
            'abonnementItems' => $abonnementItemRepository->findAll(),
        ]);
    }

}
