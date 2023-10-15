<?php
namespace App\Entity\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class EvaluationQuestionDto
{
    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?int $id = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?string $question = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?string $proposition1 = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?string $proposition2 = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?string $proposition3 = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private ?string $proposition4 = null;

    /**
     * @Groups({"read:evaluation:item"})
     */
    private array $propositionJuste = [];

    /**
     * @Groups({"read:evaluation:question"})
     */
    private ?int $evaluationId = null;

    // Add getters and setters as needed

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function getProposition1(): ?string
    {
        return $this->proposition1;
    }

    public function getProposition2(): ?string
    {
        return $this->proposition2;
    }

    public function getProposition3(): ?string
    {
        return $this->proposition3;
    }

    public function getProposition4(): ?string
    {
        return $this->proposition4;
    }

    public function getPropositionJuste(): array
    {
        return $this->propositionJuste;
    }

    public function getEvaluationId(): ?int
    {
        return $this->evaluationId;
    }

    public function setEvaluationId(?int $evaluationId): self
    {
        $this->evaluationId = $evaluationId;

        return $this;
    }
}
