<?php

namespace App\Controller\Api\Controller\Course\Quiz;

use App\Entity\QuizResult;
use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class PostController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
    ) {
    }

    public function __invoke(Request $request): QuizResult
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);

        if ($eleveConnected === null) {
            throw new BadRequestHttpException("Vous devez être connecté");
        }

        $data = $request->attributes->getIterator()['data'];
        if ($data instanceof QuizResult) {
            $data->setEleve($eleveConnected);

            return $data;
        }

        throw new BadRequestHttpException("Vous devez être connecté");
    }
}
