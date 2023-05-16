<?php

namespace App\Controller\Admin;

use App\Entity\Term;
use App\Form\TermType;
use App\Repository\TermRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/term')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class TermController extends AbstractController
{
    #[Route('/', name: 'app_admin_term_index', methods: ['GET'])]
    public function index(TermRepository $termRepository): Response
    {
        return $this->render('admin/term/index.html.twig', [
            'terms' => $termRepository->findAll(),
            'tcc' => true,
        ]);
    }

    #[Route('/new', name: 'app_admin_term_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TermRepository $termRepository): Response
    {
        $term = new Term();
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $termRepository->save($term, true);

            return $this->redirectToRoute('app_admin_term_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/term/new.html.twig', [
            'term' => $term,
            'form' => $form,
            'tcc' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_term_show', methods: ['GET'])]
    public function show(Term $term): Response
    {
        return $this->render('admin/term/show.html.twig', [
            'term' => $term,
            'tcc' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_term_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Term $term, TermRepository $termRepository): Response
    {
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $termRepository->save($term, true);

            return $this->redirectToRoute('app_admin_term_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/term/edit.html.twig', [
            'term' => $term,
            'form' => $form,
            'tcc' => true
        ]);
    }

    #[Route('/{id}', name: 'app_admin_term_delete', methods: ['POST'])]
    public function delete(Request $request, Term $term, TermRepository $termRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$term->getId(), $request->request->get('_token'))) {
            $termRepository->remove($term, true);
        }

        return $this->redirectToRoute('app_admin_term_index', [], Response::HTTP_SEE_OTHER);
    }
}
