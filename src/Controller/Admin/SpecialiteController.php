<?php

namespace App\Controller\Admin;

use App\Entity\Specialite;
use App\Form\SpecialiteType;
use App\Repository\SpecialiteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/specialite')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class SpecialiteController extends AbstractController
{
    #[Route('/', name: 'app_admin_specialite_index', methods: ['GET'])]
    public function index(SpecialiteRepository $specialiteRepository): Response
    {
        $specialite = new Specialite();
        $form = $this->createForm(SpecialiteType::class, $specialite, [
            'action' => $this->generateUrl('app_admin_specialite_new')
        ]);

        return $this->render('admin/specialite/index.html.twig', [
            'specialites' => $specialiteRepository->findAll(),
            'spc' => true,
            'isClasses' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_specialite_new', methods: ['POST'])]
    public function new(Request $request, SpecialiteRepository $specialiteRepository, SluggerInterface $slugger): Response
    {
        $specialite = new Specialite();
        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialite->setSlug($slugger->slug($specialite->getName()));
            $specialiteRepository->save($specialite, true);
        }

        return $this->redirectToRoute('app_admin_specialite_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show', name: 'app_admin_specialite_show', methods: ['GET'])]
    public function show(Specialite $specialite): Response
    {
        return $this->render('admin/specialite/show.html.twig', [
            'specialite' => $specialite,
            'spc' => true,
            'isClasses' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_specialite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Specialite $specialite, SpecialiteRepository $specialiteRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(SpecialiteType::class, $specialite, [
            'action' => $this->generateUrl('app_admin_specialite_edit', ['id' => $specialite->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialite->setSlug($slugger->slug($specialite->getName()));
            $specialiteRepository->save($specialite, true);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/specialite/edit.html.twig', [
                'specialite' => $specialite,
                'form' => $form->createView(),
                'spc' => true,
                'isClasses' => true,
            ]);
        }

        return $this->redirectToRoute('app_admin_specialite_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_admin_specialite_delete', methods: ['POST'])]
    public function delete(Request $request, Specialite $specialite, SpecialiteRepository $specialiteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$specialite->getId(), $request->request->get('_token'))) {
            $specialiteRepository->remove($specialite, true);
        }

        return $this->redirectToRoute('app_admin_specialite_index', [], Response::HTTP_SEE_OTHER);
    }
}
