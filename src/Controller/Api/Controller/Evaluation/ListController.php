<?php
namespace App\Controller\Api\Controller\Evaluation;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Repository\EvaluationRepository;
use App\Utils\Dto\EvaluationDto;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ListController extends AbstractController
{
    public function __construct(
        private EvaluationRepository $evaluationRepository
    )
    {
        
    }

    public function __invoke(Eleve $eleve): array
    {
        $classe = $eleve->getClasse();
        $evaluations = $this->evaluationRepository->findSudentEvaluationsAnnonces($classe);
        $annonces = [];
        foreach ($evaluations as $evaluation) {
            // die("ici");
            $currentDateTime = new DateTime();
            $diff = $evaluation->getStartAt()->diff($currentDateTime);
            $h = $diff->h;
            $d = $diff->days;
            $m = $diff->m;
            $y = $diff->y;
            $nbHeures = $h + $d*24 + $m*30*24 + 8760*$y;
            // dump($nbHeures, 24*6, $evaluation->getStartAt()->diff($currentDateTime));die;
            if (!$eleve->getEvaluations()->contains($evaluation) && $nbHeures <= 7*24 && $nbHeures > 0) {
            //    die;
                $annonce = [
                    'evaluation' => EvaluationDto::from($evaluation),
                    'nombreInscris' => $evaluation->getEleves()->count(),
                    'matiere' => $evaluation->getMatiere()->getName(),
                ];
                $cmp = 1;
                $annonce['eleves'] = [];
                foreach ($evaluation->getEleves() as $e) {
                    $annonce['eleves'][] = $e->getUtilisateur()->getPersonne()->getAvatarPath(); 
                    $cmp++;
                    if ($cmp > 4) {
                        break;
                    }
                }
                $annonces[] = $annonce;
            }
        }

        return $annonces;
    }
}