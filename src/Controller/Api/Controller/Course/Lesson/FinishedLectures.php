<?php
namespace App\Controller\Api\Controller\Course\Lesson;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Lecture;
use App\Repository\EleveRepository;
use App\Repository\LectureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class FinishedLectures extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private LectureRepository $lectureRepository
    ) {
    }

    public function __invoke(Eleve $eleve)
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected == null) {
            throw new BadRequestHttpException("Vous devez être connecté");
        }

        return $this->lectureRepository->findBy(['eleve' => $eleve, 'isFinished' => true]);
    }
}
