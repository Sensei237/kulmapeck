<?php 
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Eleve;
use App\Entity\Evaluation;
use App\Entity\EvaluationResultat;
use App\Repository\EleveRepository;
use App\Repository\EvaluationQuestionRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\QuizRepository;
use App\Utils\Dto\EvaluationDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostCorrectionController extends AbstractController
{ 
    public function __construct(
        private QuizRepository $quizRepository,
        private EvaluationQuestionRepository $eqr,
        private EvaluationResultatRepository $evaluationResultatRepository,
        private EleveRepository $eleveRepository,
        private Security $security
    )
    {
        
    }  

    public function __invoke(Evaluation $evaluation, Request $request)
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($request->request->get('isSubmittedExamResponses')) {
            $data = $request->request->getIterator();
            $noteQuiz = 0;
            $resultset = [];
            if (empty($data['quizzes'])) {
                return new BadRequestException("Verifier les données envoyées. Votre questionnaire est vide !");
            }
            $quizzes = $data['quizzes'];
            foreach ($quizzes as $quizze) {
                if (empty($quizze['id'])) {
                    return new BadRequestException("Données corrompues !");
                }

                $quizId = $quizze['id'];
                
                if ($evaluation->isIsGeneratedRandomQuestions()) {
                    $quiz = $this->quizRepository->find($quizId);
                } else {
                    $quiz = $this->eqr->find($quizId);
                }

                if ($quiz === null) {
                    return new BadRequestException("Corrupted data send");
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
            $this->evaluationResultatRepository->save($resultat, true);

            return ['isOk' => true, 'evaluation' => EvaluationDto::from($evaluation)];
        }
    }
}