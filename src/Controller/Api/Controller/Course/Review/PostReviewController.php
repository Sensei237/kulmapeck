<?php

namespace App\Controller\Api\Controller\Course\Review;

use App\Entity\Cours;
use App\Entity\Review;
use App\Repository\EleveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PostReviewController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security
    )
    {

    }

    public function __invoke(Cours $cours, Request $request): ?Review
    {
        $user = $this->security->getUser();
        $eleveConnected = $this->eleveRepository->findOneBy(['utilisateur' => $user]);
        if (!$eleveConnected) {
            throw $this->createAccessDeniedException('Vous devez être connecté !');
        }

        $data = $request->attributes->getIterator()['data'];
        if ($data instanceof Review) {
            $data->setEleve($eleveConnected);
            $data->setCours($cours);

            return $data;
        }

        return null;
    }
}