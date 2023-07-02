<?php

namespace App\Controller\Api\Controller\Course;

use App\Entity\Cours;
use App\Repository\EleveRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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

    public function __invoke(Cours $course, Request $request): Collection
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        return $course->getQuizzes();
    }

}
