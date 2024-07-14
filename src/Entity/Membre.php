<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:sujet:collection', 'read:sujet:item', 'read:forum:messsage:collection'])]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Forum::class, inversedBy: 'membres')]
    private Collection $forums;

    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: ForumMessage::class, orphanRemoval: true)]
    private Collection $forumMessages;

    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: Sujet::class, orphanRemoval: true)]
    private Collection $sujets;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:sujet:collection', 'read:sujet:item', 'read:forum:messsage:collection'])]
    private ?User $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: LikeMessageForum::class, orphanRemoval: true)]
    private Collection $likeMessageForums;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->forumMessages = new ArrayCollection();
        $this->sujets = new ArrayCollection();
        $this->likeMessageForums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums->add($forum);
        }

        return $this;
    }

    public function removeForum(Forum $forum): self
    {
        $this->forums->removeElement($forum);

        return $this;
    }

    /**
     * @return Collection<int, ForumMessage>
     */
    public function getForumMessages(): Collection
    {
        return $this->forumMessages;
    }

    public function addForumMessage(ForumMessage $forumMessage): self
    {
        if (!$this->forumMessages->contains($forumMessage)) {
            $this->forumMessages->add($forumMessage);
            $forumMessage->setMembre($this);
        }

        return $this;
    }

    public function removeForumMessage(ForumMessage $forumMessage): self
    {
        if ($this->forumMessages->removeElement($forumMessage)) {
            // set the owning side to null (unless already changed)
            if ($forumMessage->getMembre() === $this) {
                $forumMessage->setMembre(null);
            }
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
            $sujet->setMembre($this);
        }

        return $this;
    }

    public function removeSujet(Sujet $sujet): self
    {
        if ($this->sujets->removeElement($sujet)) {
            // set the owning side to null (unless already changed)
            if ($sujet->getMembre() === $this) {
                $sujet->setMembre(null);
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

    /**
     * @return Collection<int, LikeMessageForum>
     */
    public function getLikeMessageForums(): Collection
    {
        return $this->likeMessageForums;
    }

    public function addLikeMessageForum(LikeMessageForum $likeMessageForum): static
    {
        if (!$this->likeMessageForums->contains($likeMessageForum)) {
            $this->likeMessageForums->add($likeMessageForum);
            $likeMessageForum->setMembre($this);
        }

        return $this;
    }

    public function removeLikeMessageForum(LikeMessageForum $likeMessageForum): static
    {
        if ($this->likeMessageForums->removeElement($likeMessageForum)) {
            // set the owning side to null (unless already changed)
            if ($likeMessageForum->getMembre() === $this) {
                $likeMessageForum->setMembre(null);
            }
        }

        return $this;
    }
}
