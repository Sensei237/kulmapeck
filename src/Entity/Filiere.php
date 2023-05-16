<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection()
    ]
)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:user:item'])]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: SousSysteme::class, inversedBy: 'filieres')]
    private Collection $sousSystemes;

    #[ORM\ManyToOne(inversedBy: 'filieres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:user:item'])]
    private ?TypeEnseignement $typeEnseignement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:user:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:user:item'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'filiere', targetEntity: Specialite::class, orphanRemoval: true)]
    private Collection $specialites;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'filiere')]
    private Collection $users;

    public function __construct()
    {
        $this->sousSystemes = new ArrayCollection();
        $this->specialites = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SousSysteme>
     */
    public function getSousSystemes(): Collection
    {
        return $this->sousSystemes;
    }

    public function addSousSysteme(SousSysteme $sousSysteme): self
    {
        if (!$this->sousSystemes->contains($sousSysteme)) {
            $this->sousSystemes->add($sousSysteme);
        }

        return $this;
    }

    public function removeSousSysteme(SousSysteme $sousSysteme): self
    {
        $this->sousSystemes->removeElement($sousSysteme);

        return $this;
    }

    public function getTypeEnseignement(): ?TypeEnseignement
    {
        return $this->typeEnseignement;
    }

    public function setTypeEnseignement(?TypeEnseignement $typeEnseignement): self
    {
        $this->typeEnseignement = $typeEnseignement;

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
     * @return Collection<int, Specialite>
     */
    public function getSpecialites(): Collection
    {
        return $this->specialites;
    }

    public function addSpecialite(Specialite $specialite): self
    {
        if (!$this->specialites->contains($specialite)) {
            $this->specialites->add($specialite);
            $specialite->setFiliere($this);
        }

        return $this;
    }

    public function removeSpecialite(Specialite $specialite): self
    {
        if ($this->specialites->removeElement($specialite)) {
            // set the owning side to null (unless already changed)
            if ($specialite->getFiliere() === $this) {
                $specialite->setFiliere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFiliere($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFiliere($this);
        }

        return $this;
    }
}
