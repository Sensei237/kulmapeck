<?php

namespace App\Controller\Api\Controller\Course\Chapter;

use App\Entity\Chapitre;
use App\Repository\EleveRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class ShowQuizzesController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
    ) {
    }

    public function __invoke(Chapitre $chapitre): Collection
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            throw new BadRequestHttpException("Vous devez être connecté");
        }

        return $chapitre->getQuizzes();
    }
}
