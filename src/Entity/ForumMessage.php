<?php

namespace App\Entity;

use App\Repository\ForumMessageRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumMessageRepository::class)]
class ForumMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forumMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'forumMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sujet $sujet = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAnswer = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'forumMessages')]
    private ?self $forumMessage = null;

    #[ORM\OneToMany(mappedBy: 'forumMessage', targetEntity: self::class, cascade: ['persist', 'remove'])]
    private Collection $forumMessages;

    #[ORM\Column(nullable: true)]
    private ?bool $isResponse = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->forumMessages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->isAnswer = false;
        $this->likes = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): self
    {
        $this->membre = $membre;

        return $this;
    }

    public function getSujet(): ?Sujet
    {
        return $this->sujet;
    }

    public function setSujet(?Sujet $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function isIsAnswer(): ?bool
    {
        return $this->isAnswer;
    }

    public function setIsAnswer(?bool $isAnswer): self
    {
        $this->isAnswer = $isAnswer;

        return $this;
    }

    public function getForumMessage(): ?self
    {
        return $this->forumMessage;
    }

    public function setForumMessage(?self $forumMessage): self
    {
        $this->forumMessage = $forumMessage;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getForumMessages(): Collection
    {
        return $this->forumMessages;
    }

    public function addForumMessage(self $forumMessage): self
    {
        if (!$this->forumMessages->contains($forumMessage)) {
            $this->forumMessages->add($forumMessage);
            $forumMessage->setForumMessage($this);
        }

        return $this;
    }

    public function removeForumMessage(self $forumMessage): self
    {
        if ($this->forumMessages->removeElement($forumMessage)) {
            // set the owning side to null (unless already changed)
            if ($forumMessage->getForumMessage() === $this) {
                $forumMessage->setForumMessage(null);
            }
        }

        return $this;
    }

    public function isIsResponse(): ?bool
    {
        return $this->isResponse;
    }

    public function setIsResponse(?bool $isResponse): self
    {
        $this->isResponse = $isResponse;

        return $this;
    }
}
