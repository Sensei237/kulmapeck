<?php
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Eleve;
use App\Repository\EleveRepository;
use ArrayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class EvaluationsEleve extends AbstractController
{
    public function __construct(private EleveRepository $eleveRepository, private Security $security)
    {
        
    }

    public function __invoke(Eleve $eleve)
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected == null || $eleve !== $eleveConnected) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        return new ArrayObject([
            'evaluations' => $eleve->getEvaluations(),
            'resultats' => $eleve->getEvaluationResultats(),
        ]);
    }
}
