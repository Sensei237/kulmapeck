<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Controller\Course\Review\CourseReviewController;
use App\Controller\Api\Controller\Course\Review\PostReviewController;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: 'review/{id}/course',
            controller: CourseReviewController::class,
            read: false,
        ),
        new Post(
            uriTemplate: 'review/{id}/new',
            controller: PostReviewController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Cette route permet de poster une review, l'élève doit être connecté et l'id attendu sur l'url represente le cours <br>Rating represente la note et message est le commetaire redigé par l'élève"
            ]
        ),

    ],
    normalizationContext: ['groups' => ['read:review:collection']],
    denormalizationContext: ['groups' => ['post:review:item']],
)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:item', 'read:review:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?Eleve $eleve = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:course:item', 'read:review:collection', 'post:review:item'])]
    private ?int $rating = null;

    #[ORM\Column]
    #[Groups(['read:course:item', 'read:review:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:course:item', 'read:review:collection', 'post:review:item'])]
    private ?string $message = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
