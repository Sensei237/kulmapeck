<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Api\Controller\Course\Chapter\ShowQuizzesController;
use App\Repository\ChapitreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChapitreRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Get(
            uriTemplate: '/chapitre/{id}/quizzes',
            controller: ShowQuizzesController::class,
            read: false,
            normalizationContext: [
                'groups' => ['read:quizzes:collection']
            ],
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
    ]
)]
class Chapitre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:item', 'read:lecture:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chapitres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du chapitre ne peut être vide !")]
    #[Assert\Length(min: 10, minMessage: "Le titre du chapitre dois avoir au moins 10 caractères !")]
    #[Groups(['read:course:item', 'read:lecture:collection'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:item', 'read:lecture:collection'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est requise !")]
    #[Groups(['read:course:item'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'chapitre', cascade: ['persist', 'remove'], targetEntity: Lesson::class, orphanRemoval: true)]
    #[Groups(['read:course:item', 'read:lesson:item'])]
    private Collection $lessons;

    #[ORM\OneToMany(mappedBy: 'chapitre', targetEntity: Quiz::class, orphanRemoval: true)]
    #[Groups(['read:quizzes:collection'])]
    private Collection $quizzes;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['read:course:item', 'read:lecture:collection'])]
    private ?int $numero = null;

    #[ORM\OneToMany(mappedBy: 'chapitre', targetEntity: Lecture::class)]
    private Collection $lectures;

    #[ORM\OneToMany(mappedBy: 'chapitre', targetEntity: QuizLost::class)]
    private Collection $quizLosts;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->lectures = new ArrayCollection();
        $this->quizLosts = new ArrayCollection();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setChapitre($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getChapitre() === $this) {
                $lesson->setChapitre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setChapitre($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getChapitre() === $this) {
                $quiz->setChapitre(null);
            }
        }

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection<int, Lecture>
     */
    public function getLectures(): Collection
    {
        return $this->lectures;
    }

    public function addLecture(Lecture $lecture): self
    {
        if (!$this->lectures->contains($lecture)) {
            $this->lectures->add($lecture);
            $lecture->setChapitre($this);
        }

        return $this;
    }

    public function removeLecture(Lecture $lecture): self
    {
        if ($this->lectures->removeElement($lecture)) {
            // set the owning side to null (unless already changed)
            if ($lecture->getChapitre() === $this) {
                $lecture->setChapitre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuizLost>
     */
    public function getQuizLosts(): Collection
    {
        return $this->quizLosts;
    }

    public function addQuizLost(QuizLost $quizLost): self
    {
        if (!$this->quizLosts->contains($quizLost)) {
            $this->quizLosts->add($quizLost);
            $quizLost->setChapitre($this);
        }

        return $this;
    }

    public function removeQuizLost(QuizLost $quizLost): self
    {
        if ($this->quizLosts->removeElement($quizLost)) {
            // set the owning side to null (unless already changed)
            if ($quizLost->getChapitre() === $this) {
                $quizLost->setChapitre(null);
            }
        }

        return $this;
    }
}
