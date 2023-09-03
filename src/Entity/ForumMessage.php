<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\Controller\Course\Forum\AnswerForumMessage;
use App\Controller\Api\Controller\Course\Forum\LikeForumMessageController;
use App\Controller\Api\Controller\Course\Forum\NewForumMessage;
use App\Controller\Api\Controller\Course\Forum\SubjectMessages;
use App\Repository\ForumMessageRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ForumMessageRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/sujet/{id}/messages',
            controller: SubjectMessages::class,
            openapiContext: [
                'description' => "Cette route permet de recuperer la liste des messages d'un sujet donné !"
            ],
            normalizationContext: ['groups' => ['read:forum:messsage:collection']]
        ),
        new Put(
            uriTemplate: '/forum_message/{id}/like',
            controller: LikeForumMessageController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ],
            denormalizationContext: ['groups' => ['like:message:forum:items']],
        ),
        new Post(
            uriTemplate: '/forum-message/{id}/new-message',
            controller: NewForumMessage::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "On utilise cette route pour poster un message concernant un sujet. l'id en paramètre est l'id du sujet ou problème"
            ],
            denormalizationContext: ['groups' => ['post:forum:message:item']]
        ),
        new Post(
            uriTemplate: '/forum_message/{id}/repondre',
            controller: AnswerForumMessage::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "On utilise cette route pour répondre à un message du forum. l'id en paramètre est l'id du message au quel on souhaite répondre"
            ],
            denormalizationContext: ['groups' => ['post:forum:message:item']]
        )
    ],
    normalizationContext: ['groups' => ['read:forum:messsage:collection']]
)]
#[
    ApiFilter(SearchFilter::class, properties: ['sujet' => 'exact']),
    ApiFilter(OrderFilter::class, properties: ['$createdAt'])
]
class ForumMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forumMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'forumMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sujet $sujet = null;

    #[ORM\Column]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection', 'post:forum:message:item'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:sujet:item', 'like:message:forum:item', 'read:forum:messsage:collection'])]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private ?bool $isAnswer = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'forumMessages')]
    private ?self $forumMessage = null;

    #[ORM\OneToMany(mappedBy: 'forumMessage', targetEntity: self::class, cascade: ['persist', 'remove'])]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private Collection $forumMessages;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:sujet:item', 'read:forum:messsage:collection'])]
    private ?bool $isResponse = null;

    #[ORM\OneToMany(mappedBy: 'forumMessage', targetEntity: LikeMessageForum::class, orphanRemoval: true)]
    private Collection $likeMessageForums;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->forumMessages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->isAnswer = false;
        $this->likes = 0;
        $this->likeMessageForums = new ArrayCollection();
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

    /**
     * @return Collection<int, LikeMessageForum>
     */
    public function getLikeMessageForums(): Collection
    {
        return $this->likeMessageForums;
    }

    public function addLikeMessageForum(LikeMessageForum $likeMessageForum): static
    {
        if (!$this->likeMessageForums->contains($likeMessageForum)) {
            $this->likeMessageForums->add($likeMessageForum);
            $likeMessageForum->setForumMessage($this);
        }

        return $this;
    }

    public function removeLikeMessageForum(LikeMessageForum $likeMessageForum): static
    {
        if ($this->likeMessageForums->removeElement($likeMessageForum)) {
            // set the owning side to null (unless already changed)
            if ($likeMessageForum->getForumMessage() === $this) {
                $likeMessageForum->setForumMessage(null);
            }
        }

        return $this;
    }
}
