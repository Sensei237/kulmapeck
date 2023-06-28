<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\Controller\Course\Quiz\PostController;
use App\Repository\QuizResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuizResultRepository::class)]
#[UniqueEntity(fields: ['quiz', 'eleve'], message: 'This item exist')]
#[ApiResource(
    normalizationContext: ['groups' => ['read:quizresult:collection']],
    denormalizationContext: ['groups' => ['post:quizresult:item']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['post:quizresult:item']],
            controller: PostController::class,
            uriTemplate: '/submit/quizzes-form',
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
        new Put(
            denormalizationContext: ['groups' => ['put:quizresult:item']],
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'eleve' => 'exact',
    'quiz.cours' => 'exact',
    'quiz.chapitre' => 'exact',
])]
class QuizResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:quizresult:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizResults')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:quizresult:collection', 'post:quizresult:item'])]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(inversedBy: 'quizResults')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:quizresult:collection'])]
    private ?Eleve $eleve = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:quizresult:collection', 'post:quizresult:item', 'put:quizresult:item'])]
    private array $result = [];

    #[ORM\Column]
    #[Groups(['read:quizresult:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:quizresult:collection', 'post:quizresult:item'])]
    private ?bool $isCorrect = null;

    #[ORM\Column]
    #[Groups(['read:quizresult:collection', 'post:quizresult:item', 'put:quizresult:item'])]
    private ?float $note = null;

    #[ORM\Column]
    #[Groups(['read:quizresult:collection', 'put:quizresult:item'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

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

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(?array $result): self
    {
        $this->result = $result;

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

    public function isIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
