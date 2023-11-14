<?php

namespace App\Controller\Admin;

use App\Entity\Abonnement;
use App\Entity\AbonnementItem;
use App\Form\AbonnementItemType;
use App\Form\AbonnementType;
use App\Repository\AbonnementItemRepository;
use App\Repository\AbonnementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/abonnement')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class AbonnementController extends AbstractController
{
    #[Route('/', name: 'app_admin_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository, AbonnementItemRepository $abonnementItemRepository): Response
    {
        return $this->render('admin/abonnement/index.html.twig', [
            'plans' => $abonnementRepository->findBy([], ['montant' => 'ASC']),
            'isAbonnementController' => true,
            'abonnementItems' => $abonnementItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonnementRepository $abonnementRepository, SluggerInterface $sluggerInterface): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnement->setSlug($sluggerInterface->slug($abonnement->getLabel()));
            $abonnementRepository->save($abonnement, true);
            return $this->redirectToRoute('app_admin_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        $itemForm = $this->createForm(AbonnementItemType::class, new AbonnementItem(), [
            'action' => $this->generateUrl('app_admin_abonnement_item_new')
        ]);

        return $this->render('admin/abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
            'itemForm' => $itemForm->createView(),
            'redirectUri' => $this->generateUrl('app_admin_abonnement_new'),
            'isAbonnementController' => true,


        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('admin/abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, AbonnementRepository $abonnementRepository): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementRepository->save($abonnement, true);

            return $this->redirectToRoute('app_admin_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        $itemForm = $this->createForm(AbonnementItemType::class, new AbonnementItem(), [
            'action' => $this->generateUrl('app_admin_abonnement_item_new')
        ]);

        return $this->render('admin/abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
            'itemForm' => $itemForm->createView(),
            'redirectUri' => $this->generateUrl('app_admin_abonnement_edit', ['id' => $abonnement->getId()]),
            'isAbonnementController' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, AbonnementRepository $abonnementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->request->get('_token'))) {
            $abonnementRepository->remove($abonnement, true);
        }

        return $this->redirectToRoute('app_admin_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
