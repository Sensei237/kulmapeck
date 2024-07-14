<?php

namespace App\Controller\Admin;

use App\Entity\Evaluation;
use App\Entity\EvaluationQuestion;
use App\Form\EvaluationQuestionType;
use App\Repository\EvaluationQuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evaluation/questions')]
class EvaluationQuestionController extends AbstractController
{
    #[Route('/{slug}', name: 'app_admin_evaluation_question_index', methods: ['GET'])]
    public function index(Evaluation $evaluation): Response
    {
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render('admin/evaluation_question/index.html.twig', [
                'evaluation_questions' => $evaluation->getEvaluationQuestions(),
                'evaluation' => $evaluation,
                'isCourses' => true,
                "evc" => true,
            ]);
        }else{
            return $this->render('instructor/evaluation_question/index.html.twig', [
                'evaluation_questions' => $evaluation->getEvaluationQuestions(),
                'evaluation' => $evaluation,
                'isCourses' => true,
                "evc" => true,
            ]);
        }
       
    }

    #[Route('/{slug}/new', name: 'app_admin_evaluation_question_new', methods: ['POST', 'GET'])]
    public function new(Evaluation $evaluation, Request $request, EvaluationQuestionRepository $evaluationQuestionRepository): Response
    {
        if ($evaluation->isIsPassed()) {
            return $this->createAccessDeniedException();
        }

        $evaluationQuestion = new EvaluationQuestion();
        $form = $this->createForm(EvaluationQuestionType::class, $evaluationQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluationQuestion->setEvaluation($evaluation);
            $evaluationQuestionRepository->save($evaluationQuestion, true);

            return $this->redirectToRoute('app_admin_evaluation_question_index', ['slug' => $evaluation->getSlug()], Response::HTTP_SEE_OTHER);
        }

        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            
        return $this->render('admin/evaluation_question/new.html.twig', [
            'evaluation' => $evaluation,
            'isCourses' => true,
            "evc" => true,
            'form' => $form->createView(),
        ]);
        }else{

            return $this->render('instructor/evaluation_question/new.html.twig', [
                'evaluation' => $evaluation,
                'isCourses' => true,
                "evc" => true,
                'form' => $form->createView(),
            ]);
        }

    }

    #[Route('/{id}', name: 'app_admin_evaluation_question_show', methods: ['GET'])]
    public function show(EvaluationQuestion $evaluationQuestion): Response
    {
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render('admin/evaluation_question/show.html.twig', [
                'evaluation_question' => $evaluationQuestion,
            ]); 
        }else{
            return $this->render('instructor/evaluation_question/show.html.twig', [
                'evaluation_question' => $evaluationQuestion,
            ]);
        }
       
    }

    #[Route('/{id}/edit', name: 'app_admin_evaluation_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EvaluationQuestion $evaluationQuestion, EvaluationQuestionRepository $evaluationQuestionRepository): Response
    {
        if ($evaluationQuestion->getEvaluation()->isIsPassed()) {
            return $this->createAccessDeniedException();
        }

        $form = $this->createForm(EvaluationQuestionType::class, $evaluationQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluationQuestionRepository->save($evaluationQuestion, true);

            return $this->redirectToRoute('app_admin_evaluation_question_index', ['slug' => $evaluationQuestion->getEvaluation()->getSlug()], Response::HTTP_SEE_OTHER);
        }
        if ( $this->isGranted( 'ROLE_ADMIN' ) ) {
            return $this->render('admin/evaluation_question/edit.html.twig', [
                'evaluation_question' => $evaluationQuestion,
                'form' => $form->createView(),
                'evaluation' => $evaluationQuestion->getEvaluation(),
                'isCourses' => true,
                "evc" => true,
            ]);   
        }else{
            return $this->render('instructor/evaluation_question/edit.html.twig', [
                'evaluation_question' => $evaluationQuestion,
                'form' => $form->createView(),
                'evaluation' => $evaluationQuestion->getEvaluation(),
                'isCourses' => true,
                "evc" => true,
            ]);
        }
       
    }

    #[Route('/{id}', name: 'app_admin_evaluation_question_delete', methods: ['POST'])]
    public function delete(Request $request, EvaluationQuestion $evaluationQuestion, EvaluationQuestionRepository $evaluationQuestionRepository): Response
    {
        if ($evaluationQuestion->getEvaluation()->isIsPassed()) {
            return $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$evaluationQuestion->getId(), $request->request->get('_token'))) {
            $evaluationQuestionRepository->remove($evaluationQuestion, true);
        }

        return $this->redirectToRoute('app_admin_evaluation_question_index', ['slug' => $evaluationQuestion->getEvaluation()->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
