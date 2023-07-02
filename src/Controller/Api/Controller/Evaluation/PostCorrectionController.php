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
use ArrayObject;
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
        $data = $request->toArray();

        if (!empty($data['isSubmittedExamResponses'])) {
            $noteQuiz = 0;
            $resultset = [];
            if (empty($data['quizzes'])) {
                return new BadRequestException("Verifier les données envoyées. Votre questionnaire est vide !");
            }
            $quizzes = $data['quizzes'];
            foreach ($quizzes as $quizze) {
                if (empty($quizze['id'])) {
                    return new BadRequestException("Données corrompues ! quizze[id] n'existe pas");
                }

                $quizId = $quizze['id'];
                
                if ($evaluation->isIsGeneratedRandomQuestions()) {
                    $quiz = $this->quizRepository->find($quizId);
                } else {
                    $quiz = $this->eqr->find($quizId);
                }

                if ($quiz === null) {
                    return new BadRequestException("Données corrompu: Le quiz est null");
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

            return new ArrayObject([
                'notes' => $noteQuiz, 
                'resultats' => $resultset,
                'evaluation' => EvaluationDto::from($evaluation)
            ]);
        }

        return $this->createNotFoundException("Vous devez poster une évaluation");
    }
}