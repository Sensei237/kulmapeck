<?php

namespace App\Entity;

use App\Repository\LikeMessageForumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeMessageForumRepository::class)]
class LikeMessageForum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likeMessageForums')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumMessage $forumMessage = null;

    #[ORM\ManyToOne(inversedBy: 'likeMessageForums')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForumMessage(): ?ForumMessage
    {
        return $this->forumMessage;
    }

    public function setForumMessage(?ForumMessage $forumMessage): static
    {
        $this->forumMessage = $forumMessage;

        return $this;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): static
    {
        $this->membre = $membre;

        return $this;
    }
}
