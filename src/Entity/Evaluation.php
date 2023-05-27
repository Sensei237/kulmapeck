<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $matiere = null;

    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'evaluations')]
    private Collection $classes;

    #[ORM\Column]
    private ?\DateTime $startAt = null;

    #[ORM\Column]
    private ?\DateTime $endAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $duree = null;

    #[ORM\Column]
    private ?bool $isGeneratedRandomQuestions = null;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: EvaluationQuestion::class, orphanRemoval: true)]
    private Collection $evaluationQuestions;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $isPassed = false;

    #[ORM\ManyToMany(targetEntity: Eleve::class, inversedBy: 'evaluations')]
    private Collection $Eleves;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->evaluationQuestions = new ArrayCollection();
        $this->Eleves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    public function getMatiere(): ?Categorie
    {
        return $this->matiere;
    }

    public function setMatiere(?Categorie $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        $this->classes->removeElement($class);

        return $this;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function isIsGeneratedRandomQuestions(): ?bool
    {
        return $this->isGeneratedRandomQuestions;
    }

    public function setIsGeneratedRandomQuestions(bool $isGeneratedRandomQuestions): self
    {
        $this->isGeneratedRandomQuestions = $isGeneratedRandomQuestions;

        return $this;
    }

    /**
     * @return Collection<int, EvaluationQuestion>
     */
    public function getEvaluationQuestions(): Collection
    {
        return $this->evaluationQuestions;
    }

    public function addEvaluationQuestion(EvaluationQuestion $evaluationQuestion): self
    {
        if (!$this->evaluationQuestions->contains($evaluationQuestion)) {
            $this->evaluationQuestions->add($evaluationQuestion);
            $evaluationQuestion->setEvaluation($this);
        }

        return $this;
    }

    public function removeEvaluationQuestion(EvaluationQuestion $evaluationQuestion): self
    {
        if ($this->evaluationQuestions->removeElement($evaluationQuestion)) {
            // set the owning side to null (unless already changed)
            if ($evaluationQuestion->getEvaluation() === $this) {
                $evaluationQuestion->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isIsPassed(): ?bool
    {
        return $this->isPassed;
    }

    public function setIsPassed(bool $isPassed): self
    {
        $this->isPassed = $isPassed;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->Eleves;
    }

    public function addEleve(Eleve $eleve): self
    {
        if (!$this->Eleves->contains($eleve)) {
            $this->Eleves->add($eleve);
        }

        return $this;
    }

    public function removeEleve(Eleve $eleve): self
    {
        $this->Eleves->removeElement($eleve);

        return $this;
    }
}
