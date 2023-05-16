<?php

namespace App\Controller\Admin;

use App\Entity\Sujet;
use App\Form\SujetType;
use App\Repository\SujetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sujet')]
class SujetController extends AbstractController
{
    #[Route('/', name: 'app_admin_sujet_index', methods: ['GET'])]
    public function index(SujetRepository $sujetRepository): Response
    {
        return $this->render('admin/sujet/index.html.twig', [
            'sujets' => $sujetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_sujet_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FORUM_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function new(Request $request, SujetRepository $sujetRepository): Response
    {
        $sujet = new Sujet();
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujetRepository->save($sujet, true);

            return $this->redirectToRoute('app_admin_sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/sujet/new.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_sujet_show', methods: ['GET'])]
    public function show(Sujet $sujet): Response
    {
        return $this->render('admin/sujet/show.html.twig', [
            'sujet' => $sujet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_sujet_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_FORUM_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function edit(Request $request, Sujet $sujet, SujetRepository $sujetRepository): Response
    {
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujetRepository->save($sujet, true);

            return $this->redirectToRoute('app_admin_sujet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/sujet/edit.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_sujet_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_FORUM_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function delete(Request $request, Sujet $sujet, SujetRepository $sujetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sujet->getId(), $request->request->get('_token'))) {
            $sujetRepository->remove($sujet, true);
        }

        return $this->redirectToRoute('app_admin_sujet_index', [], Response::HTTP_SEE_OTHER);
    }
}
