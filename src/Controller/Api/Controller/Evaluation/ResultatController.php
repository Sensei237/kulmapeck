<?php
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Evaluation;
use App\Repository\EleveRepository;
use App\Repository\EvaluationQuestionRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\QuizRepository;
use App\Utils\Dto\EvaluationDto;
use ArrayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ResultatController extends AbstractController
{
    public function __construct(
        private EvaluationResultatRepository $evaluationResultatRepository,
        private QuizRepository $quizRepository,
        private EvaluationQuestionRepository $eqr,
        private EleveRepository $eleveRepository,
        private Security $security
    )
    {

    }

    public function __invoke(Evaluation $evaluation)
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        $resultat = $this->evaluationResultatRepository->findOneBy(['eleve' => $eleve, 'evaluation' => $evaluation]);
        
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
                    $quiz = $this->quizRepository->find($quizId);
                } else {
                    $quiz = $this->eqr->find($quizId);
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

        return new ArrayObject([
            'resultats' => $resultats,
            'note' => $notes,
            'evaluation' => EvaluationDto::from($evaluation)
        ]);
    }

}
