<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Assert\Length(min: 2, minMessage: "Le nom doit faire au moins 2 caractères !")]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Assert\Length(min: 5, max: 8, maxMessage: "Il faut 8 caractères maximun", minMessage: "Il faut 5 caractères minimum !")]
    private ?string $pseudo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $bornAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    private ?string $sexe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\OneToOne(mappedBy: "personne", cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $invitationCode = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $invitationLink = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'invites')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $invites;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?Pays $pays = null;

    /**
     * @var File
     */
    private $imageFile;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $joinAt = null;

    public function __construct()
    {
        $this->invites = new ArrayCollection();
        $this->joinAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getBornAt(): ?\DateTimeInterface
    {
        return $this->bornAt;
    }

    public function setBornAt(\DateTimeInterface $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): self
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getInvitationCode(): ?string
    {
        return $this->invitationCode;
    }

    public function setInvitationCode(string $invitationCode): self
    {
        $this->invitationCode = $invitationCode;

        return $this;
    }

    public function getInvitationLink()
    {
        return json_decode($this->invitationLink);
    }

    public function setInvitationLink(string $invitationLink): self
    {
        $this->invitationLink = $invitationLink;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(self $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
            $invite->setParent($this);
        }

        return $this;
    }

    public function removeInvite(self $invite): self
    {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getParent() === $this) {
                $invite->setParent(null);
            }
        }

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getNomComplet(): ? string
    {
        return $this->getFirstName() .' '. $this->getLastName();
    }

    public function getAvatarPath(): string
    {
        $avatarPath = 'uploads/images/admin/' . $this->getAvatar();
        if ($this->getUtilisateur()->getEleve() !== null) {
            $avatarPath = 'uploads/images/eleves/' . $this->getAvatar();
        }elseif ($this->getUtilisateur()->getEnseignant() !== null) {
            $avatarPath = 'uploads/images/enseignants/kyc/' . $this->getAvatar();
        }

        return $avatarPath;
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
}
