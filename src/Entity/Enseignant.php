<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
class Enseignant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:payment:collection', 'read:forum:item'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'enseignant', targetEntity: Cours::class, orphanRemoval: true)]
    private Collection $cours;

    #[ORM\OneToOne(inversedBy: 'enseignant', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:course:collection', 'read:payment:collection', 'read:forum:item'])]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'enseignants')]
    #[Groups(['read:course:collection'])]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['read:course:collection'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $diplome = null;

    #[ORM\Column(length: 255)]
    private ?string $rectoCNI = null;

    #[ORM\Column(length: 255)]
    private ?string $versoCNI = null;

    #[ORM\Column(length: 255)]
    private ?string $selfieCNI = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $emploiDuTemps = null;

    /**
     * @var File
     */
    public $diplomeFile;

    /**
     * @var File
     */
    public $rectoCNIFile;

    /**
     * @var File
     */
    public $versoCNIFile;

    /**
     * @var File
     */
    public $selfieCNIFile;

    /**
     * @var File
     */
    public $emploiDuTempsFile;

    #[ORM\Column]
    private ?bool $isValidated = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isRejected = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $review = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $joinAt = null;

    #[ORM\ManyToOne(inversedBy: 'enseignants')]
    private ?Categorie $discipline = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutMe = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCertified = null;

    #[ORM\OneToMany(mappedBy: 'relation', targetEntity: Evaluation::class)]
    private Collection $evaluations;


    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->isValidated = false;
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $cour->setEnseignant($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getEnseignant() === $this) {
                $cour->setEnseignant(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): self
    {
        $this->etablissement = $etablissement;

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

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getRectoCNI(): ?string
    {
        return $this->rectoCNI;
    }

    public function setRectoCNI(string $rectoCNI): self
    {
        $this->rectoCNI = $rectoCNI;

        return $this;
    }

    public function getVersoCNI(): ?string
    {
        return $this->versoCNI;
    }

    public function setVersoCNI(string $versoCNI): self
    {
        $this->versoCNI = $versoCNI;

        return $this;
    }

    public function getSelfieCNI(): ?string
    {
        return $this->selfieCNI;
    }

    public function setSelfieCNI(string $selfieCNI): self
    {
        $this->selfieCNI = $selfieCNI;

        return $this;
    }

    public function getEmploiDuTemps(): ?string
    {
        return $this->emploiDuTemps;
    }

    public function setEmploiDuTemps(?string $emploiDuTemps): self
    {
        $this->emploiDuTemps = $emploiDuTemps;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function isIsRejected(): ?bool
    {
        return $this->isRejected;
    }

    public function setIsRejected(?bool $isRejected): self
    {
        $this->isRejected = $isRejected;

        return $this;
    }

    public function getReview(): ?int
    {
        return $this->review;
    }

    public function setReview(?int $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getJoinAt(): ?\DateTimeImmutable
    {
        return $this->joinAt;
    }

    public function setJoinAt(?\DateTimeImmutable $joinAt): self
    {
        $this->joinAt = $joinAt;

        return $this;
    }

    public function getAvatar(): ?string
    {

        return "uploads/images/enseignants/kyc/" . $this->getUtilisateur()->getPersonne()->getAvatar();
    }

    public function getDiscipline(): ?Categorie
    {
        return $this->discipline;
    }

    public function setDiscipline(?Categorie $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(?string $aboutMe): self
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    public function isIsCertified(): ?bool
    {
        return $this->isCertified;
    }

    public function setIsCertified(?bool $isCertified): self
    {
        $this->isCertified = $isCertified;

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setRelation($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getRelation() === $this) {
                $evaluation->setRelation(null);
            }
        }

        return $this;
    }
}
