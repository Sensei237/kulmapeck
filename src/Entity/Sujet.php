<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\Controller\Course\Forum\ForumSubjects;
use App\Controller\Api\Controller\Course\Forum\PostSubjectController;
use App\Controller\Api\Controller\Course\Forum\SujetResoluController;
use App\Repository\SujetRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SujetRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/forum/{id}/subjects',
            controller: ForumSubjects::class,
            openapiContext: [
                'description' => "Cette route permet de récuperer la liste des sujets d'un forum donné en foction de son id"
            ]
        ),
        new Get(),
        new Post(
            uriTemplate: '/sujet/{id}/new',
            controller: PostSubjectController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Cette route permet de créer un sujet dans le forum. On precise l'id du forum dans l'url"
            ]
        ),
        new Put(
            uriTemplate: '/sujet/{id}/mark-solved',
            controller: SujetResoluController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Cette route permet de marquer qu'un sujet est resolu. l'id en paramètre est l'id du message permettant de resoudre le problème"
            ]
        )
    ],
    normalizationContext: ['groups' => ['read:sujet:collection']],
    denormalizationContext: ['groups' => ['post:sujet:item']]
)]
#[
    ApiFilter(SearchFilter::class, properties: [
        'forum' => 'exact',
        'forum.cours' => 'exact',
    ]),
    ApiFilter(OrderFilter::class, properties: ['createdAt']),
    ApiFilter(BooleanFilter::class, properties: ['isSolved'])
]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:sujet:collection'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'sujet', targetEntity: ForumMessage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['read:sujet:item'])]
    private Collection $forumMessages;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:sujet:collection'])]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Forum $forum = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:sujet:collection', 'post:sujet:item'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:sujet:collection', 'put:sujet:item'])]
    private ?bool $isSolved = null;

    #[ORM\Column]
    #[Groups(['read:sujet:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:sujet:collection'])]
    private ?string $reference = null;

    public function __construct()
    {
        $this->forumMessages = new ArrayCollection();
        $this->isSolved = false;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ForumMessage>
     */
    public function getForumMessages(): Collection
    {
        return $this->forumMessages;
    }

    public function addForumMessage(ForumMessage $forumMessage): self
    {
        if (!$this->forumMessages->contains($forumMessage)) {
            $this->forumMessages->add($forumMessage);
            $forumMessage->setSujet($this);
        }

        return $this;
    }

    public function removeForumMessage(ForumMessage $forumMessage): self
    {
        if ($this->forumMessages->removeElement($forumMessage)) {
            // set the owning side to null (unless already changed)
            if ($forumMessage->getSujet() === $this) {
                $forumMessage->setSujet(null);
            }
        }

        return $this;
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

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

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

    public function isIsSolved(): ?bool
    {
        return $this->isSolved;
    }

    public function setIsSolved(bool $isSolved): self
    {
        $this->isSolved = $isSolved;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
