<?php

namespace App\Controller\Front;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\SujetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_front_forum_index')]
    #[Route('/{slug}', name: 'app_front_forum_index_category')]
    public function index(Categorie $categorie = null, Request $request, SujetRepository $sujetRepository, PaginatorInterface $paginator, CategorieRepository $categorieRepository): Response
    {
        if ($request->query->get('search')) {
            $subjects = $sujetRepository->searchSubjects($request->query->get('search'));
        } elseif ($categorie) {
            $subjects = $sujetRepository->findByCourseCategory($categorie);
        }else {
            $subjects = $sujetRepository->findBy([], ['isSolved' => 'DESC']);
        }
        return $this->render('front/forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'isForumMainPage' => true,
            'search' => $request->query->get('search'),
            'categories' => $categorieRepository->findBCategoriesForum(15),
            'subjects' => $paginator->paginate($subjects, $request->query->get('page', 1), 15)
        ]);
    }
}
