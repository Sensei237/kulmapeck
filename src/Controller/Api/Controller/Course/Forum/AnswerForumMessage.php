<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\ForumMessage;
use App\Repository\EleveRepository;
use App\Repository\MembreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class AnswerForumMessage extends AbstractController
{
    public function __construct(
        private EleveRepository $eleveRepository,
        private Security $security,
        private MembreRepository $membreRepository
    )
    {

    }

    public function __invoke(ForumMessage $forumMessage, Request $request): ?ForumMessage
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté");
        }

        $sujet = $forumMessage->getSujet();
        $forum = $sujet->getForum();
        $membre = $this->membreRepository->findOneBy(['utilisateur' => $user]);
        if (!$membre || !$membre->getForums()->contains($forum)) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas écrire dans ce forum");
        }

        if ($forumMessage->isIsAnswer()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas répondre à une réponse");
        }

        $data = $request->toArray();
        $content = $data['content'];
        $fm = new ForumMessage();
        $fm->setMembre($membre)
            ->setSujet($sujet)
            ->setForumMessage($forumMessage)
            ->setIsAnswer(true)
            ->setIsResponse(false)
            ->setContent($content);

        return $fm;
    }
}