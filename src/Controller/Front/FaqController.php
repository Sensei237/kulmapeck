<?php

namespace App\Controller\Front;

use App\Repository\FAQRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/frequenly-ask-questions')]
class FaqController extends AbstractController
{
    #[Route('/', name: 'app_front_faq')]
    public function index(Request $request, PaginatorInterface $paginator, FAQRepository $FAQRepository): Response
    {
        return $this->render('front/faq/index.html.twig', [
            'controller_name' => 'FaqController',
            'faqs' => $paginator->paginate($FAQRepository->findFAQs(true), $request->query->getInt('page', 1), 20)
        ]);
    }
}
