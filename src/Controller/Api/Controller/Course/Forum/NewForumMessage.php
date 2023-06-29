<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\ForumMessage;
use App\Entity\Sujet;
use App\Repository\EleveRepository;
use App\Repository\MembreRepository;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NewForumMessage extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private MembreRepository $membreRepository
    )
    {

    }

    public function __invoke(Sujet $sujet, Request $request): ?ForumMessage
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new BadRequestHttpException("Vous devez être connecté", null, 403);
        }

        $forum = $sujet->getForum();
        $membre = $this->membreRepository->findOneBy(['utilisateur' => $user]);
        if (!$membre || !$membre->getForums()->contains($forum)) {
            throw new BadRequestException("Vous ne pouvez pas écrire dans ce forum");
        }

        $data = $request->attributes->getIterator()['data'];
        if ($data instanceof ForumMessage) {
            $data->setSujet($sujet)
                ->setMembre($membre);
            return $data;
        }

        return null;
    }
}