<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\ForumMessage;
use App\Repository\EleveRepository;
use App\Repository\MembreRepository;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LikeForumMessageController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private MembreRepository $membreRepository
    )
    {

    }

    public  function __invoke(ForumMessage $forumMessage): ForumMessage
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new BadRequestHttpException("Vous devez être connecté", null, 403);
        }

        $membre = $this->membreRepository->findOneBy(['utilisateur' => $user]);
        $forum = $forumMessage->getSujet()->getForum();
        if (!$membre || !$membre->getForums()->contains($forum)) {
            throw new UnauthorizedException("Vous ne pouvez pas écrire dans ce forum");
        }

        $forumMessage->setLikes($forumMessage->getLikes()+1);
        return $forumMessage;
    }

}