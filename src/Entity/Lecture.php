<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\Controller\Course\Lesson\FinishedController;
use App\Controller\Api\Controller\Course\Lesson\FinishedLectures;
use App\Controller\Api\Controller\Course\StudentLectureController;
use App\Repository\LectureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LectureRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:lecture:collection']],
    operations: [
        new GetCollection(
            uriTemplate: '/student/{id}/lectures',
            controller: StudentLectureController::class,
            read: false,
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
        new GetCollection(
            uriTemplate: '/student/{id}/finished-lectures',
            controller: FinishedLectures::class,
            read: false,
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
        new Get(),
        new Get(
            uriTemplate: '/lecture/{id}/finished',
            controller: FinishedController::class,
            read: false,
            normalizationContext: [
                'groups' => ['read:lesson:item']
            ],
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        )
    ]
)]
class Lecture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:lecture:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lectures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne(inversedBy: 'lectures')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read:lecture:collection'])]
    private ?Lesson $lesson = null;

    #[ORM\Column]
    #[Groups(['read:lecture:collection'])]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:lecture:collection'])]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    #[Groups(['read:lecture:collection'])]
    private ?bool $isFinished = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:lecture:collection'])]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'lectures')]
    #[Groups(['read:lecture:collection'])]
    private ?Chapitre $chapitre = null;

    #[ORM\ManyToOne(inversedBy: 'lectures')]
    #[Groups(['read:lecture:collection'])]
    private ?Cours $cours = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:lecture:collection'])]
    private ?float $note = null;

    public function __construct()
    {
        $this->startAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function isIsFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): self
    {
        $this->isFinished = $isFinished;

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

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): self
    {
        $this->chapitre = $chapitre;

        return $this;
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

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): self
    {
        $this->note = $note;

        return $this;
    }
}
