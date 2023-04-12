<?php

namespace App\Entity;

use App\Repository\QuizLostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizLostRepository::class)]
class QuizLost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizLosts')]
    private ?Cours $cours = null;

    #[ORM\ManyToOne(inversedBy: 'quizLosts')]
    private ?Chapitre $chapitre = null;

    #[ORM\ManyToOne(inversedBy: 'quizLosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $attempt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $nextAt = null;

    #[ORM\Column]
    private ?bool $isOk = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): self
    {
        $this->chapitre = $chapitre;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getAttempt(): ?int
    {
        return $this->attempt;
    }

    public function setAttempt(int $attempt): self
    {
        $this->attempt = $attempt;

        return $this;
    }

    public function getLastAt(): ?\DateTimeImmutable
    {
        return $this->lastAt;
    }

    public function setLastAt(\DateTimeImmutable $lastAt): self
    {
        $this->lastAt = $lastAt;

        return $this;
    }

    public function getNextAt(): ?\DateTimeImmutable
    {
        return $this->nextAt;
    }

    public function setNextAt(\DateTimeImmutable $nextAt): self
    {
        $this->nextAt = $nextAt;

        return $this;
    }

    public function isIsOk(): ?bool
    {
        return $this->isOk;
    }

    public function setIsOk(bool $isOk): self
    {
        $this->isOk = $isOk;

        return $this;
    }
}
