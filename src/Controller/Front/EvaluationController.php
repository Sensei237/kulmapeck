<?php

namespace App\Controller\Front;

use App\Entity\Eleve;
use App\Entity\Evaluation;
use App\Entity\EvaluationResultat;
use App\Repository\EleveRepository;
use App\Repository\EvaluationQuestionRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evaluations')]
class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_front_evaluation')]
    public function index(): Response
    {
        return $this->render('front/evaluation/index.html.twig', [
            'controller_name' => 'EvaluationController',
        ]);
    }

    #[Route('/{slug}/s-inscrire', name: 'app_front_evaluation_inscription')]
    public function sinscrire(Request $request, Evaluation $evaluation, EleveRepository $eleveRepository): Response 
    {
        $user = $this->getUser();
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve === null) {
            throw $this->createAccessDeniedException();
        }

        if (!$eleve->isIsPremium()) {
            $this->addFlash('info', "Vous devez être premium pour passer les examens organisés");
            return $this->redirectToRoute('app_plan');
        }

        if ($eleve->getEvaluations()->contains($evaluation)) {
            throw $this->createAccessDeniedException("Vous êtes déjà inscris !");
        }

        if (!$evaluation->getClasses()->contains($eleve->getClasse())) {
            throw $this->createAccessDeniedException("Vous né pouvez pas vous inscrire à ce test car il n'est pas pour votre niveau");
        }

        $evaluation->addEleve($eleve);
        $eleve->addEvaluation($evaluation);
        $eleveRepository->save($eleve, true);

        $request->getSession()->set('annonce', null);
        $request->getSession()->set('hasAnnonces', false);
        $request->getSession()->set('showAnnonces', true);

        $this->addFlash('success', 'Vous avez souscris à participer au test de harmonisé. vérifiez dans votre tableau de bord !');

        return $this->redirect($request->server->getHeaders()['REFERER']);
    }

    #[Route('/{slug}/hide-annonce', name: 'app_front_evaluation_hide_annonce')]
    public function hideAnnonce(Request $request, Evaluation $evaluation): Response
    {
        $hideAnnonces = $request->getSession()->get('hideAnnonces', []);
        $hideAnnonces[] = $evaluation->getId();

        $request->getSession()->set('hideAnnonces', $hideAnnonces);

        $request->getSession()->set('annonce', null);
        $request->getSession()->set('hasAnnonces', false);
        $request->getSession()->set('showAnnonces', true);

        return $this->redirect($request->server->getHeaders()['REFERER']);
    }

    #[Route('/{slug}/composer', name: 'app_front_evaluation_begin', methods: ['GET', 'POST'])]
    public function composer(Evaluation $evaluation, EvaluationResultatRepository $evaluationResultatRepository, QuizRepository $quizRepository, EvaluationQuestionRepository $eqr, EntityManagerInterface $entityManager, EleveRepository $eleveRepository, Request $request): Response
    {
        $user = $this->getUser();
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve === null) {
            throw $this->createAccessDeniedException("Seul les élèves sont autorisés !");
        }

        if (!$eleve->isIsPremium()) {
            $this->addFlash('info', "Vous devez être premium pour passer les examens organisés");
            return $this->redirectToRoute('app_plan');
        }

        if (!$evaluation->getClasses()->contains($eleve->getClasse()) || !$eleve->getEvaluations()->contains($evaluation)) {
            throw $this->createAccessDeniedException("Cette évaluation ne figure pas dans votre liste ou ne correspond pas à votre classe !");
        }

        if ($evaluation->isIsPassed()) {
            throw $this->createAccessDeniedException("Vous ne pouvez plus passer ce test car la date est passée");
        }

        if ($evaluationResultatRepository->findOneBy(['eleve' => $eleve, 'evaluation' => $evaluation])) {
            $this->addFlash('infoEvaluation', "Vous avez déjà passé ce test. Vous ne pouvez plus le refaire !");
            return $this->redirectToRoute('app_front_evaluation_result', ['slug' => $evaluation->getSlug()]);
        }

        if ($request->request->get('isSubmittedExamResponses')) {
            $data = $request->request->getIterator();
            $noteQuiz = 0;
            $resultset = [];
            // dd($data['quizzes']);
            $quizzes = $data['quizzes'];
            foreach ($quizzes as $quizze) {
                if (empty($quizze['id'])) {
                    throw $this->createNotFoundException("Données corrompues !");
                }

                $quizId = $quizze['id'];
                
                if ($evaluation->isIsGeneratedRandomQuestions()) {
                    $quiz = $quizRepository->find($quizId);
                } else {
                    $quiz = $eqr->find($quizId);
                }

                if ($quiz === null) {
                    throw $this->createAccessDeniedException("Corrupted data send");
                }

                $isCorrect = false;
                $note = 0;

                if (isset( $quizze['reponses'])) {
                    $results = $quizze['reponses'];
                    if($results == $quiz->getPropositionJuste()) {
                        $isCorrect = true;
                        $note = 20/count($quizzes);
                        $noteQuiz += $note;
                    }
                }
                $quizze['isCorrect'] = $isCorrect;
                $resultset['quizzes'][] = $quizze;
            }
            $resultset['notes'] = $noteQuiz;

            $resultat = new EvaluationResultat();
            $resultat->setEleve($eleve)->setEvaluation($evaluation)->setContents($resultset)->setNoteObtenue($noteQuiz);
            $evaluationResultatRepository->save($resultat, true);

            return $this->redirectToRoute('app_front_evaluation_result', ['slug' => $evaluation->getSlug()]);
        }

        // ON CHARGE L'EPREUVE
        $epreuve = [];
        if (!$evaluation->isIsGeneratedRandomQuestions()) {
            $epreuve = $evaluation->getEvaluationQuestions();
        }else {
            $epreuve = $quizRepository->findRandomQuizzes($evaluation);
            // dd("icid", $epreuve);die;
        }

        return $this->render('front/evaluation/epreuve.html.twig', [
            'evaluation' => $evaluation,
            'epreuve' => $epreuve,
        ]);
    }

    #[Route('/{slug}/resultats', name: 'app_front_evaluation_result')]
    public function afficherResultats(Evaluation $evaluation, QuizRepository $quizRepository, EvaluationQuestionRepository $eqr, EleveRepository $eleveRepository, EvaluationResultatRepository $evaluationResultatRepository): Response 
    {
        $user = $this->getUser();
        $eleve = $eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve === null) {
            throw $this->createAccessDeniedException("Seul les élèves sont autorisés !");
        }

        $resultat = $evaluationResultatRepository->findOneBy(['eleve' => $eleve, 'evaluation' => $evaluation]);
        
        $resultats = [];
        $notes = null;

        if ($resultat !== null) {
            $contents = $resultat->getContents();
            if (!isset($contents['quizzes']) || !isset($contents['notes'])) {
                throw $this->createNotFoundException();
            }

            $notes = $contents['notes'];

            foreach ($contents['quizzes'] as $quizze) {
                if (!isset($quizze['id'])) {
                    throw $this->createNotFoundException();
                }
                $quizId = $quizze['id'];
                $reponses = isset($quizze['reponses']) ? $quizze['reponses'] : [];
                if ($evaluation->isIsGeneratedRandomQuestions()) {
                    $quiz = $quizRepository->find($quizId);
                } else {
                    $quiz = $eqr->find($quizId);
                }
                if ($quiz === null) {
                    throw $this->createNotFoundException();
                }
                $item = [
                    'quiz' => $quiz,
                    'reponses' => $reponses,
                    'isCorrect' => isset($quizze['isCorrect']) ? $quizze['isCorrect'] : false,
                ];
                $resultats[] = $item;
            }
        }

        // dump($resultats, $notes, $resultat);die;

        return $this->render('front/evaluation/resultat.html.twig', [
            'evaluation' => $evaluation,
            'eleve' => $eleve,
            'resultat' => $resultat,
            'resultats' => $resultats,
            'noteObtenue' => $notes,
        ]);
    }
}
