<?php

namespace App\Controller\Admin;

use App\Entity\AbonnementItem;
use App\Form\AbonnementItemType;
use App\Repository\AbonnementItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/abonnement/item')]
class AbonnementItemController extends AbstractController
{
    #[Route('/', name: 'app_admin_abonnement_item_index', methods: ['GET'])]
    public function index(AbonnementItemRepository $abonnementItemRepository): Response
    {
        return $this->render('admin/abonnement_item/index.html.twig', [
            'abonnement_items' => $abonnementItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_abonnement_item_new', methods: ['POST'])]
    public function new(Request $request, AbonnementItemRepository $abonnementItemRepository): Response
    {
        $abonnementItem = new AbonnementItem();
        $form = $this->createForm(AbonnementItemType::class, $abonnementItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementItemRepository->save($abonnementItem, true);

            if ($request->request->get('redirectUri')) {
                return $this->redirect($request->request->get('redirectUri'));
            }

            return $this->redirectToRoute('app_admin_abonnement_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/abonnement_item/new.html.twig', [
            'abonnement_item' => $abonnementItem,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonnement_item_show', methods: ['GET'])]
    public function show(AbonnementItem $abonnementItem): Response
    {
        return $this->render('admin/abonnement_item/show.html.twig', [
            'abonnement_item' => $abonnementItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_abonnement_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AbonnementItem $abonnementItem, AbonnementItemRepository $abonnementItemRepository): Response
    {
        $form = $this->createForm(AbonnementItemType::class, $abonnementItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementItemRepository->save($abonnementItem, true);

            return $this->redirectToRoute('app_admin_abonnement_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/abonnement_item/edit.html.twig', [
            'abonnement_item' => $abonnementItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonnement_item_delete', methods: ['POST'])]
    public function delete(Request $request, AbonnementItem $abonnementItem, AbonnementItemRepository $abonnementItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnementItem->getId(), $request->request->get('_token'))) {
            $abonnementItemRepository->remove($abonnementItem, true);
        }

        return $this->redirectToRoute('app_admin_abonnement_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
