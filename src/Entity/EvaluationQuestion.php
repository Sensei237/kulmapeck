<?php

namespace App\Entity;

use App\Repository\EvaluationQuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EvaluationQuestionRepository::class)]
class EvaluationQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:evaluation:item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:evaluation:item'])]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:evaluation:item'])]
    private ?string $proposition1 = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:evaluation:item'])]
    private ?string $proposition2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:evaluation:item'])]
    private ?string $proposition3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:evaluation:item'])]
    private ?string $proposition4 = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(['read:evaluation:item'])]
    private array $propositionJuste = [];

    #[ORM\ManyToOne(inversedBy: 'evaluationQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:evaluation:question'])]
        private ?Evaluation $evaluation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getProposition1(): ?string
    {
        return $this->proposition1;
    }

    public function setProposition1(string $proposition1): self
    {
        $this->proposition1 = $proposition1;

        return $this;
    }

   

    public function getProposition2(): ?string
    {
        return $this->proposition2;
    }

    public function setProposition2(string $proposition2): self
    {
        $this->proposition2 = $proposition2;

        return $this;
    }


    public function getProposition3(): ?string
    {
        return $this->proposition3;
    }

    public function setProposition3(?string $proposition3): self
    {
        $this->proposition3 = $proposition3;

        return $this;
    }

    public function getProposition4(): ?string
    {
        return $this->proposition4;
    }

    public function setProposition4(?string $proposition4): self
    {
        $this->proposition4 = $proposition4;

        return $this;
    }

    public function getPropositionJuste(): array
    {
        return $this->propositionJuste;
    }

    public function setPropositionJuste(array $propositionJuste): self
    {
        $this->propositionJuste = $propositionJuste;

        return $this;
    }
    
   /**
     * @return ?Evaluation
     * @Groups({"read:evaluation:question"})
     */
    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }
}
