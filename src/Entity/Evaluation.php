<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Controller\Evaluation\EvaluationsEleve;
use App\Controller\Api\Controller\Evaluation\InscriptionController;
use App\Controller\Api\Controller\Evaluation\ListController;
use App\Controller\Api\Controller\Evaluation\PostCorrectionController;
use App\Controller\Api\Controller\Evaluation\QuestionnaireController;
use App\Controller\Api\Controller\Evaluation\ResultatController;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/evaluations/{id}/list',
            controller: ListController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => 'ID = Id de eleve'
            ],
            normalizationContext: [
                'groups' => ['read:evaluation:collection']
            ],
            read: false
        ),
        new GetCollection(
            uriTemplate: '/evaluations/student/{id}',
            controller: EvaluationsEleve::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Permet de recuperer la liste des évaluations d'un élève"
            ],
            normalizationContext: [
                'groups' => ['read:evaluation:collection']
            ],
            read: false
        ),
        new Post(
            uriTemplate: '/evaluations/{id}/inscription',
            controller: InscriptionController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => 'id = evaluation_id'
            ],
            write: false
        ),
        new Get(
            uriTemplate: '/evaluations/{id}/questionnaire',
            controller: QuestionnaireController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => 'id = evaluation_id'
            ],
            normalizationContext: [
                'groups' => ['read:evaluation:question', 'read:evaluation:item']
            ],
            read: false
        ),
        new Post(
            uriTemplate: '/evaluations/{id}/corrige',
            controller: PostCorrectionController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => 'id = evaluation_id'
            ],
            normalizationContext: [
                'groups' => ['read:evaluation:question'],
                'description' => 'id = evaluation_id'
            ],
            write: false
        ),
        new Get(
            uriTemplate: '/evaluations/{id}/resultat',
            controller: ResultatController::class,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Cette route permet de récupérer la correction d'une évaluation. id = evaluation_id"
            ],
            normalizationContext: [
                'groups' => ['read:evaluation:item']
            ],
            read: false
        )

    ]
)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:evaluation:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:evaluation:collection'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:evaluation:collection'])]
    private ?Categorie $matiere = null;

    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'evaluations')]
    #[Groups(['read:evaluation:collection'])]
    private Collection $classes;

    #[ORM\Column(nullable:true)]
    #[Groups(['read:evaluation:collection'])]
    private ?\DateTime $startAt = null;

    #[ORM\Column(nullable:true)]
    #[Groups(['read:evaluation:collection'])]
    private ?\DateTime $endAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:evaluation:collection'])]
    private ?int $duree = null;

    #[ORM\Column]
    #[Groups(['read:evaluation:collection'])]
    private ?bool $isGeneratedRandomQuestions = null;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: EvaluationQuestion::class, orphanRemoval: true)]
    #[Groups(['read:evaluation:item'])]
    private Collection $evaluationQuestions;

    #[ORM\Column(length: 255)]
    #[Groups(['read:evaluation:collection'])]
    private ?string $slug = null;

    #[ORM\Column]
    #[Groups(['read:evaluation:collection'])]
    private ?bool $isPassed = false;

    #[ORM\ManyToMany(targetEntity: Eleve::class, inversedBy: 'evaluations')]
    private Collection $Eleves;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: EvaluationResultat::class, orphanRemoval: true)]
    private Collection $results;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    private ?Enseignant $enseignant = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPublished = null;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->evaluationQuestions = new ArrayCollection();
        $this->Eleves = new ArrayCollection();
        $this->results = new ArrayCollection();
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

    /**
     * @return Collection<int, EvaluationResultat>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(EvaluationResultat $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results->add($result);
            $result->setEvaluation($this);
        }

        return $this;
    }

    public function removeResult(EvaluationResultat $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getEvaluation() === $this) {
                $result->setEvaluation(null);
            }
        }

        return $this;
    }
   /**
     * @return ?Enseignant
     * @Groups({"read:evaluation:collection", "read:evaluation:item"})
     */
    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignant $enseignant): static
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
