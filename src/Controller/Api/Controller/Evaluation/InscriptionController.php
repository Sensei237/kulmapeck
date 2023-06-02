<?php
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Evaluation;
use App\Repository\EleveRepository;
use App\Utils\Dto\EvaluationDto;
use ArrayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class InscriptionController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security
        )
    {
        
    }

    public function __invoke(Evaluation $evaluation): ArrayObject
    {
        $user = $this->security->getUser();
        $eleve = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if (!$eleve->isIsPremium()) {
            throw $this->createAccessDeniedException("Vous devez être premium vous inscrire à une évaluation");
        }

        if ($eleve->getEvaluations()->contains($evaluation)) {
            throw $this->createAccessDeniedException("Vous êtes déjà inscris !");
        }

        if (!$evaluation->getClasses()->contains($eleve->getClasse())) {
            throw $this->createAccessDeniedException("Vous né pouvez pas vous inscrire à ce test car il n'est pas pour votre niveau");
        }

        $evaluation->addEleve($eleve);
        $eleve->addEvaluation($evaluation);
        $this->eleveRepository->save($eleve, true);

        return new ArrayObject(
            [
                'isOk' => true,
                'evaluation' => EvaluationDto::from($evaluation)
            ]
        );
    }

}