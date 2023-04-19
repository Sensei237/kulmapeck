<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:quiz:collection']],
    operations: [
        new GetCollection(),
        new Get(),
    ]
)]
#[ApiFilter(
    SearchFilter::class, properties: ['cours' => 'exact', 'chapitre' => 'exact']
)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:item', 'read:quizzes:collection', 'read:quiz:collection', 'read:quizresult:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Chapitre $chapitre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:item', 'read:quizzes:collection','read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:item', 'read:quizzes:collection','read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Proposition::class, orphanRemoval: true)]
    private Collection $propositions;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:course:item', 'read:quizzes:collection', 'read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $proposition1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:course:item', 'read:quizzes:collection', 'read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $proposition2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:course:item', 'read:quizzes:collection', 'read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $proposition3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:course:item', 'read:quizzes:collection', 'read:quiz:collection', 'read:quizresult:collection'])]
    private ?string $proposition4 = null;

    #[ORM\Column]
    #[Groups(['read:course:item', 'read:quizzes:collection','read:quiz:collection', 'read:quizresult:collection'])]
    private array $propositionJuste = [];

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: QuizResult::class, orphanRemoval: true)]
    private Collection $quizResults;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    private ?Cours $cours = null;

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
        $this->quizResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

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

    /**
     * @return Collection<int, Proposition>
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): self
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions->add($proposition);
            $proposition->setQuiz($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): self
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getQuiz() === $this) {
                $proposition->setQuiz(null);
            }
        }

        return $this;
    }

    public function getProposition1(): ?string
    {
        return $this->proposition1;
    }

    public function setProposition1(?string $proposition1): self
    {
        $this->proposition1 = $proposition1;

        return $this;
    }

    public function getProposition2(): ?string
    {
        return $this->proposition2;
    }

    public function setProposition2(?string $proposition2): self
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
     * @return Collection<int, QuizResult>
     */
    public function getQuizResults(): Collection
    {
        return $this->quizResults;
    }

    public function addQuizResult(QuizResult $quizResult): self
    {
        if (!$this->quizResults->contains($quizResult)) {
            $this->quizResults->add($quizResult);
            $quizResult->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizResult(QuizResult $quizResult): self
    {
        if ($this->quizResults->removeElement($quizResult)) {
            // set the owning side to null (unless already changed)
            if ($quizResult->getQuiz() === $this) {
                $quizResult->setQuiz(null);
            }
        }

        return $this;
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

}
