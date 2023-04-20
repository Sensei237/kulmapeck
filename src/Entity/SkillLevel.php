<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SkillLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SkillLevelRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection()
    ]
)]
class SkillLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:classe:collection', 'read:user:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:classe:collection', 'read:user:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:classe:collection', 'read:user:item'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'skillLevel', targetEntity: Classe::class)]
    private Collection $classes;

    #[ORM\OneToMany(mappedBy: 'skillLevel', targetEntity: Cours::class)]
    private Collection $cours;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->cours = new ArrayCollection();
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
            $class->setSkillLevel($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getSkillLevel() === $this) {
                $class->setSkillLevel(null);
            }
        }

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
            $cour->setSkillLevel($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getSkillLevel() === $this) {
                $cour->setSkillLevel(null);
            }
        }

        return $this;
    }
}
