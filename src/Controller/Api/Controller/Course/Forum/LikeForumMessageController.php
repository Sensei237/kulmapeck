<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\ForumMessage;
use App\Entity\LikeMessageForum;
use App\Repository\EleveRepository;
use App\Repository\ForumMessageRepository;
use App\Repository\LikeMessageForumRepository;
use App\Repository\MembreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LikeForumMessageController extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private MembreRepository $membreRepository,
        private LikeMessageForumRepository $likeMessageForumRepository,
        private ForumMessageRepository $forumMessageRepository
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
            throw $this->createAccessDeniedException("Vous ne pouvez pas écrire dans ce forum");
        }

        if ($this->likeMessageForumRepository->findOneBy(['membre' => $membre, 'forumMessage' => $forumMessage]) === null) {
            $this->forumMessageRepository->save($forumMessage->setLikes($forumMessage->getLikes()+1));
            $lmf = new LikeMessageForum();
            $lmf->setMembre($membre)->setForumMessage($forumMessage, true);
            $this->likeMessageForumRepository->save($lmf, true);
        }

        return $forumMessage;
    }

}