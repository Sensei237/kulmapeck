<?php

namespace App\Controller\Admin;

use App\Entity\Eleve;
use App\Entity\Reponse;
use App\Form\EleveType;
use App\Repository\EleveRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/students')]
#[Security("is_granted('ROLE_STUDENTS_MANAGER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class EleveController extends AbstractController
{
    #[Route('/', name: 'app_admin_eleve_index', methods: ['GET'])]
    public function index(EleveRepository $eleveRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        $eleves = $eleveRepository->search($request->query->get('search'));

        return $this->render('admin/eleve/index.html.twig', [
            'eleves' => $paginatorInterface->paginate($eleves, $request->query->getInt('page', 1), 15),
            'elc' => true,
            'search' => $request->query->get('search')
        ]);
    }

    #[Route('/new', name: 'app_admin_eleve_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function new(Request $request, EleveRepository $eleveRepository): Response
    {
        $eleve = new Eleve();
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eleveRepository->save($eleve, true);

            return $this->redirectToRoute('app_admin_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/eleve/new.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{reference}', name: 'app_admin_eleve_show', methods: ['GET'])]
    public function show(Eleve $eleve): Response
    {
        return $this->render('admin/eleve/show.html.twig', [
            'eleve' => $eleve,
            'elc' => true,
        ]);
    }

    
    #[Route('/{id}/edit', name: 'app_eleve_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_UNAUTHORIZE')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
    public function edit(Request $request, Eleve $eleve, EleveRepository $eleveRepository): Response
    {
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eleveRepository->save($eleve, true);

            return $this->redirectToRoute('app_admin_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_eleve_delete', methods: ['POST'])]
    public function delete(Request $request, Eleve $eleve, EleveRepository $eleveRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eleve->getId(), $request->request->get('_token'))) {
            $eleveRepository->remove($eleve, true);
        }

        return $this->redirectToRoute('app_admin_eleve_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{reference}/block', name: 'app_admin_eleve_block', methods: ['GET'])]
    public function block(Eleve $eleve, EleveRepository $eleveRepository): Response
    {
        $eleve->getUtilisateur()->setIsBlocked(!$eleve->getUtilisateur()->isIsBlocked());
        $eleveRepository->save($eleve, true);

        return $this->redirectToRoute('app_admin_eleve_index', [], Response::HTTP_SEE_OTHER);
    }
}
