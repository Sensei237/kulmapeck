<?php

namespace App\Controller\Api\Controller\Course\Forum;

use App\Entity\Forum;
use App\Entity\ForumMessage;
use App\Entity\Sujet;
use App\Repository\EleveRepository;
use App\Repository\ForumMessageRepository;
use App\Repository\MembreRepository;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SujetResoluController extends AbstractController
{
    public function __construct(
        private ForumMessageRepository $forumMessageRepository,
        private Security $security,
        private MembreRepository $membreRepository
    )
    {

    }

    public function __invoke(ForumMessage $forumMessage, Request $request): Sujet
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté");
        }
        $sujet = $forumMessage->getSujet();
        $forum = $sujet->getForum();
        $membre = $this->membreRepository->findOneBy(['utilisateur' => $user]);
        if (!$membre || !$membre->getForums()->contains($forum) || $sujet->getMembre() !== $membre) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas changer le statut de ce sujet");
        }

        $sujet->setIsSolved(true);
        $forumMessage->setIsResponse(true);
        $this->forumMessageRepository->save($forumMessage, true);

        return $sujet;
    }
}