<?php

namespace App\Controller\Admin;

use App\Entity\SousSysteme;
use App\Form\SousSystemeType;
use App\Repository\SousSystemeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/sous/systeme')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class SousSystemeController extends AbstractController
{
    private const ACTIVE_PAGE = 'sss';

    #[Route('/', name: 'app_admin_sous_systeme_index', methods: ['GET'])]
    public function index(SousSystemeRepository $sousSystemeRepository): Response
    {
        $sousSysteme = new SousSysteme();
        $form = $this->createForm(SousSystemeType::class, $sousSysteme, [
            'action' => $this->generateUrl('app_admin_sous_systeme_new'),
            'method' => 'POST'
        ]);

        return $this->render('admin/sous_systeme/index.html.twig', [
            'sous_systemes' => $sousSystemeRepository->findAll(),
            'sss' => self::ACTIVE_PAGE,
            'isInstitution' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_sous_systeme_new', methods: ['POST'])]
    public function new(Request $request, SousSystemeRepository $sousSystemeRepository, SluggerInterface $slugger): Response
    {
        $sousSysteme = new SousSysteme();
        $form = $this->createForm(SousSystemeType::class, $sousSysteme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sousSysteme->setSlug($slugger->slug($sousSysteme->getName()));
            $sousSystemeRepository->save($sousSysteme, true);
        }

        return $this->redirectToRoute('app_admin_sous_systeme_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_sous_systeme_show', methods: ['GET'])]
    public function show(SousSysteme $sousSysteme): Response
    {
        return $this->render('admin/sous_systeme/show.html.twig', [
            'sous_systeme' => $sousSysteme,
            'sss' => self::ACTIVE_PAGE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_sous_systeme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SousSysteme $sousSysteme, SousSystemeRepository $sousSystemeRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SousSystemeType::class, $sousSysteme, [
            'method' => 'POST', 
            'action' => $this->generateUrl('app_admin_sous_systeme_edit', [
                'id' => $sousSysteme->getId(),
            ])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sousSysteme->setSlug($slugger->slug($sousSysteme->getName()));
            $sousSystemeRepository->save($sousSysteme, true);

            return $this->redirectToRoute('app_admin_sous_systeme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/sous_systeme/edit.html.twig', [
            'sous_systeme' => $sousSysteme,
            'form' => $form->createView(),
            'sss' => self::ACTIVE_PAGE,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_sous_systeme_delete', methods: ['POST'])]
    public function delete(Request $request, SousSysteme $sousSysteme, SousSystemeRepository $sousSystemeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sousSysteme->getId(), $request->request->get('_token'))) {
            $sousSystemeRepository->remove($sousSysteme, true);
        }

        return $this->redirectToRoute('app_admin_sous_systeme_index', [], Response::HTTP_SEE_OTHER);
    }
}
