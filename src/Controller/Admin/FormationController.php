<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/formation')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_admin_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('admin/formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
            'isFormationController' => true,
            'isCourses' => true,
        ]);
    }

    #[Route('/new', name: 'app_admin_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormationRepository $formationRepository, SluggerInterface $slugger): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formation->setSlug($slugger->slug(time(). ' '.$formation->getName()));
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_admin_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'isFormationController' => true,
            'isCourses' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('admin/formation/show.html.twig', [
            'formation' => $formation,
            'isFormationController' => true,
            'isCourses' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, FormationRepository $formationRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$formation->setSlug($slugger->slug(time() . ' ' . $formation->getName()));
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_admin_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'isFormationController' => true,
            'isCourses' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute('app_admin_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
