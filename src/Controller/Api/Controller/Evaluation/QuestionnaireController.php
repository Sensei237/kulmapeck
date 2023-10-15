<?php
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Evaluation;
use App\Repository\EleveRepository;
use App\Repository\EvaluationResultatRepository;
use App\Repository\QuizRepository;
use App\Utils\Dto\EvaluationDto;
use ArrayObject;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
#[AsController]
class QuestionnaireController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private EvaluationResultatRepository $evaluationResultatRepository,
        private EntityManagerInterface $entityManager,
        private QuizRepository $quizRepository,
        private SerializerInterface $serializer

    )
    {
        
    }

    public function __invoke(Evaluation $evaluation): ArrayObject
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleve == null) {
            throw $this->createAccessDeniedException("Vous devez être connecté");
        }

        if (!$eleve->isIsPremium()) {
            throw $this->createAccessDeniedException("Vous devez être premium pour passer les examens organisés");
        }

        if (!$evaluation->getClasses()->contains($eleve->getClasse()) || !$eleve->getEvaluations()->contains($evaluation)) {
            throw $this->createAccessDeniedException("Cette évaluation ne figure pas dans votre liste ou ne correspond pas à votre classe !");
        }

        if ($evaluation->isIsPassed()) {
            throw $this->createAccessDeniedException("Vous ne pouvez plus passer ce test car la date est passée");
        }

        if ($this->evaluationResultatRepository->findOneBy(['eleve' => $eleve, 'evaluation' => $evaluation])) {
            throw $this->createAccessDeniedException("Vous avez déjà passé ce test. Vous ne pouvez plus le refaire ! Consulter votre tableau de bord pour voir la correction");
        }

        $currentDate = new \DateTimeImmutable();
        $formattedStartDate = $evaluation->getStartAt()->format('Y-m-d');
        
        if ($formattedStartDate > $currentDate->format('Y-m-d')) {
            throw $this->createAccessDeniedException("L'évaluation n'est pas encore disponible ! La date de début est {$formattedStartDate} et actuel {$currentDate->format('Y-m-d')}");
        }
        
        $epreuve = [];
        if (!$evaluation->isIsGeneratedRandomQuestions()) {
            $epreuve = $evaluation->getEvaluationQuestions();
        }else {
            $epreuve = $this->quizRepository->findRandomQuizzes($evaluation);
        }

        $normalizedData = $this->serializer->normalize(
            [
                'evaluation' => EvaluationDto::from($evaluation),
                'epreuve' => $epreuve,
            ],
            null,
            [AbstractNormalizer::GROUPS => ['read:evaluation:collection','read:evaluation:item']]
        );

        return new ArrayObject($normalizedData);

    }
}