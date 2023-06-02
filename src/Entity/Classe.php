<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
#[
    ApiResource(
        operations: [
            new GetCollection(
                normalizationContext: ['groups' => ['read:classe:collection']]
            )
        ]
    ),
    ApiFilter(SearchFilter::class, properties: [
        'specialite' => 'exact',
        'specialite.filiere' => 'exact',
        'sousSysteme' => 'exact',

    ])
]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:classe:collection', 'read:user:item', 'read:evaluation:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[Groups(['read:user:item'])]
    private ?Specialite $specialite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de classe ne peut être vide !")]
    #[Assert\NotNull(message: "Le nom de classe ne peut être nul !")]
    #[Groups(['read:classe:collection', 'read:user:item', 'read:evaluation:collection'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:classe:collection', 'read:user:item', 'read:evaluation:collection'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Cours::class, mappedBy: 'classe')]
    private Collection $cours;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Eleve::class)]
    private Collection $eleves;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: Exam::class)]
    private Collection $exams;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[Groups(['read:classe:collection', 'read:user:item'])]
    private ?SkillLevel $skillLevel = null;

    #[ORM\ManyToOne(inversedBy: 'classes')]
    #[Groups(['read:classe:collection', 'read:user:item'])]
    private ?SousSysteme $sousSysteme = null;

    #[ORM\ManyToMany(targetEntity: Evaluation::class, mappedBy: 'classes')]
    private Collection $evaluations;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->addClasse($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            $cour->removeClasse($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setClasse($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getClasse() === $this) {
                $elefe->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams->add($exam);
            $exam->setClasse($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getClasse() === $this) {
                $exam->setClasse(null);
            }
        }

        return $this;
    }

    public function getSkillLevel(): ?SkillLevel
    {
        return $this->skillLevel;
    }

    public function setSkillLevel(?SkillLevel $skillLevel): self
    {
        $this->skillLevel = $skillLevel;

        return $this;
    }

    public function getSousSysteme(): ?SousSysteme
    {
        return $this->sousSysteme;
    }

    public function setSousSysteme(?SousSysteme $sousSysteme): self
    {
        $this->sousSysteme = $sousSysteme;

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->addClass($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            $evaluation->removeClass($this);
        }

        return $this;
    }
}
