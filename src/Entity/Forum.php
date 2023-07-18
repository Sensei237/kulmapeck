<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Api\Controller\Course\Forum\CourseForum;
use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ForumRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:forum:item']],
    operations: [
        new Get(), 
        new Get(
            uriTemplate: '/cours/{id}/forum',
            controller: CourseForum::class,
            read: false,
        )
    ]
)]
class Forum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:forum:item', 'read:course:collection'])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:forum:item'])]
    private ?Cours $cours = null;

    #[ORM\ManyToMany(targetEntity: Membre::class, mappedBy: 'forums', cascade: ['persist', 'remove'])]
    private Collection $membres;

    #[ORM\OneToMany(mappedBy: 'forum', targetEntity: Sujet::class, orphanRemoval: true)]
    private Collection $sujets;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->sujets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->addForum($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            $membre->removeForum($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Sujet>
     */
    public function getSujets(): Collection
    {
        return $this->sujets;
    }

    public function addSujet(Sujet $sujet): self
    {
        if (!$this->sujets->contains($sujet)) {
            $this->sujets->add($sujet);
            $sujet->setForum($this);
        }

        return $this;
    }

    public function removeSujet(Sujet $sujet): self
    {
        if ($this->sujets->removeElement($sujet)) {
            // set the owning side to null (unless already changed)
            if ($sujet->getForum() === $this) {
                $sujet->setForum(null);
            }
        }

        return $this;
    }
}
