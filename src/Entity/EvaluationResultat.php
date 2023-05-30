<?php

namespace App\Entity;

use App\Repository\EvaluationResultatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationResultatRepository::class)]
class EvaluationResultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evaluation $evaluation = null;

    #[ORM\ManyToOne(inversedBy: 'evaluationResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $contents = [];

    #[ORM\Column]
    private ?float $noteObtenue = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEvaluationAt = null;

    public function __construct()
    {
        $this->dateEvaluationAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

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

    public function getContents(): array
    {
        return $this->contents;
    }

    public function setContents(array $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    public function getNoteObtenue(): ?float
    {
        return $this->noteObtenue;
    }

    public function setNoteObtenue(float $noteObtenue): self
    {
        $this->noteObtenue = $noteObtenue;

        return $this;
    }

    public function getDateEvaluationAt(): ?\DateTimeImmutable
    {
        return $this->dateEvaluationAt;
    }

    public function setDateEvaluationAt(\DateTimeImmutable $dateEvaluationAt): self
    {
        $this->dateEvaluationAt = $dateEvaluationAt;

        return $this;
    }
}
