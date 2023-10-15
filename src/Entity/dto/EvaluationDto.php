<?php 

namespace App\Entity\Dto;

use App\Entity\Categorie;
use App\Entity\Enseignant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

class EvaluationDto
{
    #[Groups(['read:evaluation:collection'])]
    private ?int $id = null;

    #[Groups(['read:evaluation:collection'])]
    private ?string $titre = null;

    private ?string $description = null;

    #[Groups(['read:evaluation:collection'])]
    private ?Categorie $matiere = null;

    #[Groups(['read:evaluation:collection'])]
    private \DateTime $startAt;

    #[Groups(['read:evaluation:collection'])]
    private \DateTime $endAt;

    #[Groups(['read:evaluation:collection'])]
    private int $duree;

    #[Groups(['read:evaluation:collection'])]
    private ?bool $isGeneratedRandomQuestions = null;

    #[Groups(['read:evaluation:collection'])]
    private Collection $classes;

    #[Groups(['read:evaluation:item'])]
    private Collection $evaluationQuestions;

    #[Groups(['read:evaluation:collection'])]
    private ?string $slug = null;

    #[Groups(['read:evaluation:collection'])]
    private ?bool $isPassed = false;

    #[Groups(['read:evaluation:collection'])]
    private Collection $Eleves;

    #[Groups(['read:evaluation:collection'])]
    private Collection $results;

    private ?Enseignant $enseignant = null;

    private ?bool $isPublished = null;

    public function __construct()
    {
        // Initialize collections and other necessary properties
        $this->classes = new ArrayCollection();
        $this->evaluationQuestions = new ArrayCollection();
        $this->Eleves = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    // Include getter methods for the properties

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMatiere(): ?Categorie
    {
        return $this->matiere;
    }

    public function getStartAt(): \DateTime
    {
        return $this->startAt;
    }

    public function getEndAt(): \DateTime
    {
        return $this->endAt;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function isIsGeneratedRandomQuestions(): ?bool
    {
        return $this->isGeneratedRandomQuestions;
    }

    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function getEvaluationQuestions(): Collection
    {
        return $this->evaluationQuestions;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function isIsPassed(): ?bool
    {
        return $this->isPassed;
    }

    public function getEleves(): Collection
    {
        return $this->Eleves;
    }

    public function getResults(): Collection
    {
        return $this->results;
    }

    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }
}
