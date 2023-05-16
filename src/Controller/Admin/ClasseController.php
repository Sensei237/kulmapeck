<?php

namespace App\Controller\Admin;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/classe')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'app_admin_classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe, [
            'action' => $this->generateUrl('app_admin_classe_new')
        ]);

        return $this->render('admin/classe/index.html.twig', [
            'classes' => $classeRepository->findAll(),
            'clc' => true,
            'isClasses' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_admin_classe_new', methods: ['POST'])]
    public function new(Request $request, ClasseRepository $classeRepository, SluggerInterface $slugger): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe->setSlug($slugger->slug($classe->getName()));
            $classeRepository->save($classe, true);
        }

        return $this->redirectToRoute('app_admin_classe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('admin/classe/show.html.twig', [
            'classe' => $classe,
            'clc' => true,
            'gc' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, ClasseRepository $classeRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ClasseType::class, $classe, [
            'method' => "POST",
            'action' => $this->generateUrl('app_admin_classe_edit', ['id' => $classe->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe->setSlug($slugger->slug($classe->getName()));
            $classeRepository->save($classe, true);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/classe/edit.html.twig', [
                'classe' => $classe,
                'form' => $form->createView(),
                'clc' => true,
                'gc' => true,
            ]);
        }

        return $this->redirectToRoute('app_admin_classe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, ClasseRepository $classeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->request->get('_token'))) {
            $classeRepository->remove($classe, true);
        }

        return $this->redirectToRoute('app_admin_classe_index', [], Response::HTTP_SEE_OTHER);
    }
}
