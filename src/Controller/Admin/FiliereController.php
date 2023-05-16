<?php

namespace App\Controller\Admin;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/filiere')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class FiliereController extends AbstractController
{
    #[Route('/', name: 'app_admin_filiere_index', methods: ['GET'])]
    public function index(FiliereRepository $filiereRepository): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere, [
            'action' => $this->generateUrl('app_admin_filiere_new'),
        ]);

        return $this->render('admin/filiere/index.html.twig', [
            'filieres' => $filiereRepository->findAll(),
            'fic' => true,
            'isClasses' => true,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_admin_filiere_new', methods: ['POST'])]
    public function new(Request $request, FiliereRepository $filiereRepository, SluggerInterface $slugger): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiere->setSlug($slugger->slug($filiere->getName()));
            $filiereRepository->save($filiere, true);
        }

        return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show', name: 'app_admin_filiere_show', methods: ['GET'])]
    public function show(Filiere $filiere): Response
    {
        return $this->render('admin/filiere/show.html.twig', [
            'filiere' => $filiere,
            'fic' => true,
            'gc' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_filiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiere $filiere, FiliereRepository $filiereRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiere->setSlug($slugger->slug($filiere->getName()));
            $filiereRepository->save($filiere, true);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/filiere/edit.html.twig', [
                'filiere' => $filiere,
                'form' => $form->createView(),
                'fic' => true,
                'gc' => true,
            ]);
        }

        return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_admin_filiere_delete', methods: ['POST'])]
    public function delete(Request $request, Filiere $filiere, FiliereRepository $filiereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), $request->request->get('_token'))) {
            $filiereRepository->remove($filiere, true);
        }

        return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
