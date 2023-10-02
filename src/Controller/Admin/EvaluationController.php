<?php

namespace App\Controller\Admin;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use App\Repository\EvaluationResultatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'app_admin_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('admin/evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository->findBy([], ['endAt' => 'DESC'], 30),
            'isCourses' => true,
            'evc' => true,
        ]);
    }

    #[Route('/new', name: 'app_admin_evaluation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvaluationRepository $evaluationRepository, SluggerInterface $sluggerInterface): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setSlug($sluggerInterface->slug($evaluation->getTitre()) . '-' . time());
            $evaluationRepository->save($evaluation, true);

            return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form->createView(),
            'isCourses' => true,
            'evc' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_evaluation_show', methods: ['GET'])]
    public function show(Evaluation $evaluation, EvaluationResultatRepository $evaluationResultatRepository): Response
    {
        return $this->render('admin/evaluation/show.html.twig', [
            'evaluation' => $evaluation,
            'isCourses' => true,
            'evc' => true,
            'resultats' => $evaluationResultatRepository->findBy(['evaluation' => $evaluation], ['noteObtenue' => 'DESC'])
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_evaluation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository): Response
    {
        if ($evaluation->isIsPassed()) {
            return $this->createAccessDeniedException();
        }

        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluationRepository->save($evaluation, true);

            return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/evaluation/edit.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form->createView(),
            'isCourses' => true,
            'evc' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_evaluation_delete', methods: ['POST'])]
    public function delete(Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository): Response
    {
        if ($evaluation->isIsPassed()) {
            return $this->createAccessDeniedException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$evaluation->getId(), $request->request->get('_token'))) {
            $evaluationRepository->remove($evaluation, true);
        }

        return $this->redirectToRoute('app_admin_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }

}
