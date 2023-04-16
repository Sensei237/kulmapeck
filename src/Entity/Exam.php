<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

use App\Repository\ExamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExamRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['read:exam:collection', 'read:exam:item']
            ],
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['read:exam:collection']
            ],

        ),
    ],
    normalizationContext: [
        'groups' => ['read:exam:collection']
    ],
),
ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'language' => 'exact',
    'category' => 'exact',
    'classe' => 'exact',
    'classe.specialite' => 'exact',
    'classe.specialite.filiere' => 'exact',
    'classe.skillLevel' => 'exact',
]),
ApiFilter(BooleanFilter::class, properties: ['isValidated', 'isIsPublished']),
ApiFilter(DateFilter::class, properties: ['publishedAt']),
ApiFilter(OrderFilter::class, properties: ['id', 'publishedAt'], arguments: ['orderParameterName' => 'order']),
]
class Exam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:exam:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:exam:item'])]
    private ?string $sujet = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:exam:item'])]
    private ?string $correction = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:exam:collection'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:exam:collection'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read:exam:collection'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column]
    #[Groups(['read:exam:collection'])]
    private ?bool $isPublished = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:exam:collection'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:exam:collection'])]
    private ?string $reference = null;

    /**
     * @var File
     */
    private $sujetFile;

    /**
     * @var File
     */
    private $correctionFile;

    /**
     * @var File
     */
    public File $image;

    #[ORM\Column(nullable: true)]
    private ?bool $isValidated = null;

    #[ORM\Column(length: 50)]
    private ?string $duration = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    private ?Categorie $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFile = null;

    #[ORM\Column(length: 50)]
    private ?string $language = null;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->isPublished = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getCorrection(): ?string
    {
        return $this->correction;
    }

    public function setCorrection(?string $correction): self
    {
        $this->correction = $correction;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function setSujetFile(File $sujetFile = null)
    {
        $this->sujetFile = $sujetFile;

        return $this;
    }

    public function getSujetFile()
    {
        return $this->sujetFile;
    }

    public function setCorrectionFile(File $correctionFile = null)
    {
        $this->correctionFile = $correctionFile;

        return $this;
    }

    public function getCorrectionFile()
    {
        return $this->correctionFile;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(?bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getCategory(): ?Categorie
    {
        return $this->category;
    }

    public function setCategory(?Categorie $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

}
