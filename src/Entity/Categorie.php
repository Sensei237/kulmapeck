<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['read:category:item', 'read:category:collection']
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['read:category:collection']
            ],
            paginationClientItemsPerPage: true,
            paginationItemsPerPage: 30,
            paginationMaximumItemsPerPage: 25,
        ),
    ],
    normalizationContext: [
        'groups' => ['read:category:collection']
    ],
)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:category:collection', 'read:evaluation:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:collection', 'read:category:collection', 'read:evaluation:collection'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:collection', 'read:category:collection', 'read:evaluation:collection'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Cours::class)]
    private Collection $cours;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:category:collection'])]
    private ?string $imageFile = null;

    /**
     * @var File
     */
    public $image;

    #[ORM\Column(nullable: false)]
    #[Groups(['read:category:collection'])]
    private ?bool $isSubCategory = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subCategories')]
    private ?self $category = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: self::class)]
    private Collection $subCategories;

    #[ORM\OneToMany(mappedBy: 'discipline', targetEntity: Enseignant::class)]
    private Collection $enseignants;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Exam::class)]
    private Collection $exams;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Evaluation::class, orphanRemoval: true)]
    private Collection $evaluations;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->subCategories = new ArrayCollection();
        $this->enseignants = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->isSubCategory = false;
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $cour->setCategorie($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getCategorie() === $this) {
                $cour->setCategorie(null);
            }
        }

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

    public function isIsSubCategory(): ?bool
    {
        return $this->isSubCategory;
    }

    public function setIsSubCategory(?bool $isSubCategory): self
    {
        $this->isSubCategory = $isSubCategory;

        return $this;
    }

    public function getCategory(): ?self
    {
        return $this->category;
    }

    public function setCategory(?self $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(self $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(self $subCategory): self
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enseignant>
     */
    public function getEnseignants(): Collection
    {
        return $this->enseignants;
    }

    public function addEnseignant(Enseignant $enseignant): self
    {
        if (!$this->enseignants->contains($enseignant)) {
            $this->enseignants->add($enseignant);
            $enseignant->setDiscipline($this);
        }

        return $this;
    }

    public function removeEnseignant(Enseignant $enseignant): self
    {
        if ($this->enseignants->removeElement($enseignant)) {
            // set the owning side to null (unless already changed)
            if ($enseignant->getDiscipline() === $this) {
                $enseignant->setDiscipline(null);
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
            $exam->setCategory($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getCategory() === $this) {
                $exam->setCategory(null);
            }
        }

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
            $evaluation->setMatiere($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getMatiere() === $this) {
                $evaluation->setMatiere(null);
            }
        }

        return $this;
    }
}
