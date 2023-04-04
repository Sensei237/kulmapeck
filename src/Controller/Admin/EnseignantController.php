<?php

namespace App\Controller\Admin;

use App\Entity\Enseignant;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/instructors')]
class EnseignantController extends AbstractController
{
    #[Route('/', name: 'app_admin_enseignant_index', methods: ['GET'])]
    public function index(Request $request, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface): Response
    {
        $enseignants = $enseignantRepository->findBy(['isValidated' => true], ['isValidated' => 'DESC']);

        return $this->render('admin/enseignant/index.html.twig', [
            'enseignants' => $paginatorInterface->paginate($enseignants, $request->query->getInt('page', 1), 10),
            'isEnseignants' => true,
            'eni' => true,
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);
    }

    #[Route('/requests', name: 'app_admin_enseignant_request', methods: ['GET'])]
    public function request(Request $request, EnseignantRepository $enseignantRepository, PaginatorInterface $paginatorInterface): Response
    {
        $filter = $request->get('filter');
        if ($filter !== null) {
            if ($filter === 'accepted') {
                $enseignants = $enseignantRepository->findBy(['isValidated' => true]);
            }elseif ($filter === 'rejected') {
                $enseignants = $enseignantRepository->findBy(['isRejected' => true]);
            } else {
                $enseignants = [];
            }
        }else {
            $enseignants = $enseignantRepository->findAll();
        }

        

        return $this->render('admin/enseignant/request.html.twig', [
            'enseignants' => $paginatorInterface->paginate($enseignants, $request->query->getInt('page', 1), 10),
            'isEnseignants' => true,
            'enr' => true,
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);
    }

    #[Route('/request/{reference}/accept', name: 'app_admin_enseignant_accept_request', methods: ['GET'])]
    public function accept(Enseignant $enseignant, EnseignantRepository $enseignantRepository)
    {
        $enseignant->getUtilisateur()->setIsBlocked(false);
        $enseignantRepository->save($enseignant->setIsValidated(true)->setIsRejected(false), true);

        return $this->redirectToRoute('app_admin_enseignant_request');
    }

    #[Route('/request/{reference}/reject', name: 'app_admin_enseignant_reject_request', methods: ['GET'])]
    public function reject(Enseignant $enseignant, EnseignantRepository $enseignantRepository)
    {
        $enseignant->getUtilisateur()->setIsBlocked(true);
        $enseignantRepository->save($enseignant->setIsValidated(false)->setIsRejected(true), true);

        return $this->redirectToRoute('app_admin_enseignant_request');
    }

    #[Route('/new', name: 'app_admin_enseignant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnseignantRepository $enseignantRepository): Response
    {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enseignantRepository->save($enseignant, true);

            return $this->redirectToRoute('app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/enseignant/new.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{reference}', name: 'app_admin_enseignant_show', methods: ['GET'])]
    public function show(Enseignant $enseignant, Request $request, PaginatorInterface $paginatorInterface, ReviewRepository $reviewRepository): Response
    {
        return $this->render('admin/enseignant/show.html.twig', [
            'enseignant' => $enseignant,
            'courses' => $enseignant->getCours(),
            'reviews' => $reviewRepository->findInstrutorReviews($enseignant),
            'isEnseignants' => true,
            'eni' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_enseignant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enseignant $enseignant, EnseignantRepository $enseignantRepository): Response
    {
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enseignantRepository->save($enseignant, true);

            return $this->redirectToRoute('app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_enseignant_delete', methods: ['POST'])]
    public function delete(Request $request, Enseignant $enseignant, EnseignantRepository $enseignantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enseignant->getId(), $request->request->get('_token'))) {
            $enseignantRepository->remove($enseignant, true);
        }

        return $this->redirectToRoute('app_admin_enseignant_index', [], Response::HTTP_SEE_OTHER);
    }
}
