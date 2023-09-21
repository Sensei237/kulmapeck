<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CoursRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\Controller\Course\DetailsController;
use App\Controller\Api\Controller\Course\ShowQuizzesController;
use App\Controller\Api\Controller\Course\StartCourseController;
use App\Controller\Api\Controller\Course\StudentCourseController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
#[
    ApiResource(
        operations: [
            new GetCollection(
                options: ['isActivated' => true],
                normalizationContext: ['groups' => ['read:course:collection']]
            ),
            new Get(
                uriTemplate: '/cours/{id}/details',
                controller: DetailsController::class,
                normalizationContext: ['groups' => ['read:course:collection', 'read:course:item']],
                write: true,
                name: 'course_details'
            ),
            new GetCollection(
                uriTemplate: '/student/{id}/courses',
                controller: StudentCourseController::class,
                openapiContext: [
                    'security' => [['bearerAuth' => []]]
                ],
                normalizationContext: [
                    'groups' => ['read:course:collection']
                ],
                read: false
            ),
            new Get(
                uriTemplate: '/cours/{id}/start',
                controller: StartCourseController::class,
                openapiContext: [
                    'security' => [['bearerAuth' => []]]
                ],
                normalizationContext: [
                    'groups' => ['read:lesson:item']
                ],
                read: false
            ),
            new Get(
                uriTemplate: '/cours/{id}/quizzes',
                controller: ShowQuizzesController::class,
                openapiContext: [
                    'security' => [['bearerAuth' => []]]
                ],
                normalizationContext: [
                    'groups' => ['read:quizzes:collection']
                ],
                read: false
            ),
        ],
        normalizationContext: [
            'groups' => ['read:course:collection']
        ],
        paginationClientItemsPerPage: true,
        paginationItemsPerPage: 10,
        paginationMaximumItemsPerPage: 20,
    ),
    ApiFilter(
        SearchFilter::class, properties: [
            'id' => 'exact',
            'categorie' => 'exact',
            'intitule' => 'partial',
            'language' => 'exact',
            'classe' => 'exact',
            'classe.specialite' => 'exact',
            'classe.specialite.filiere' => 'exact',
            'classe.skillLevel' => 'exact'
        ]
    ),
    ApiFilter(BooleanFilter::class, properties: ['isFree', 'isValidated']),
    ApiFilter(OrderFilter::class, properties: ['id', 'intitule'], arguments: ['orderParameterName' => 'order']),
    ApiFilter(DateFilter::class, properties: ['publishedAt']),

]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:payment:collection', 'read:lecture:collection', 'read:forum:item'])]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'cours')]
    #[Groups(['read:course:collection'])]
    private Collection $classe;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:collection', 'read:payment:collection', 'read:lecture:collection', 'read:forum:item'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:collection', 'read:forum:item'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:item'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:collection'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:forum:item'])]
    private ?bool $isPublished = null;

    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?bool $isFree = null;

    #[ORM\OneToMany(mappedBy: 'cours', cascade: ['persist', 'remove'], targetEntity: Chapitre::class, orphanRemoval: true)]
    #[Groups(['read:course:item', 'read:lesson:item'])]
    private Collection $chapitres;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:course:collection', 'read:forum:item'])]
    private ?Enseignant $enseignant = null;

    #[ORM\ManyToMany(targetEntity: Eleve::class, mappedBy: 'cours')]
    
    private Collection $eleves;

    #[Groups(['read:course:item'])]
    private $numberOfStudents;

    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'cours')]
    private Collection $formations;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?string $niveauDifficulte = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ne peut être vide !")]
    #[Assert\NotNull(message: "Ne peut être nul !")]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?string $dureeApprentissage = null;

    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:course:collection', 'update:course:item'])]
    private ?int $vues = null;

    #[ORM\Column]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?bool $isValidated = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?int $montantAbonnement = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[Groups(['read:course:collection'])]
    private ?Categorie $categorie = null;

    #[ORM\OneToOne(mappedBy: 'cours', cascade: ['persist', 'remove'])]
    #[Groups(['read:course:collection'])]
    private ?Media $media = null;

    #[ORM\OneToOne(mappedBy: 'cours', cascade: ['persist', 'remove'])]
    #[Groups(['read:course:collection'])]
    private ?Forum $forum = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: FAQ::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['read:course:item'])]
    private Collection $fAQs;

    #[ORM\Column(length: 100)]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?string $language = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:course:collection', 'read:payment:collection'])]
    private ?int $numberOfLessons = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?string $tags = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?bool $isRejected = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?int $review = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\ManyToMany(targetEntity: PaymentMethod::class, inversedBy: 'cours')]
    #[Groups(['read:course:item'])]
    private Collection $paymentMethods;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Lecture::class)]
    private Collection $lectures;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Quiz::class)]
    #[Groups(['read:quizzes:collection'])]
    private Collection $quizzes;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: QuizLost::class)]
    private Collection $quizLosts;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    private ?SkillLevel $skillLevel = null;

    public function __construct()
    {
        $this->classe = new ArrayCollection();
        $this->chapitres = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->isPublished = false;
        $this->isValidated = false;
        $this->isFree = true;
        $this->vues = 0;
        $this->createdAt = new DateTimeImmutable();
        $this->fAQs = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->review = 0;
        $this->paymentMethods = new ArrayCollection();
        $this->lectures = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->quizLosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classe $classe): self
    {
        if (!$this->classe->contains($classe)) {
            $this->classe->add($classe);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): self
    {
        $this->classe->removeElement($classe);

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function isIsFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    /**
     * @return Collection<int, Chapitre>
     */
    public function getChapitres(): Collection
    {
        return $this->chapitres;
    }

    public function addChapitre(Chapitre $chapitre): self
    {
        if (!$this->chapitres->contains($chapitre)) {
            $this->chapitres->add($chapitre);
            $chapitre->setCours($this);
        }

        return $this;
    }

    public function removeChapitre(Chapitre $chapitre): self
    {
        if ($this->chapitres->removeElement($chapitre)) {
            // set the owning side to null (unless already changed)
            if ($chapitre->getCours() === $this) {
                $chapitre->setCours(null);
            }
        }

        return $this;
    }

    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignant $enseignant): self
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->addCour($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->removeElement($elefe)) {
            $elefe->removeCour($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->addCour($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCour($this);
        }

        return $this;
    }

    public function getNiveauDifficulte(): ?string
    {
        return $this->niveauDifficulte;
    }

    public function setNiveauDifficulte(string $niveauDifficulte): self
    {
        $this->niveauDifficulte = $niveauDifficulte;

        return $this;
    }

    public function getDureeApprentissage(): ?string
    {
        return $this->dureeApprentissage;
    }

    public function setDureeApprentissage(string $dureeApprentissage): self
    {
        $this->dureeApprentissage = $dureeApprentissage;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVues(): ?int
    {
        return $this->vues;
    }

    public function setVues(int $vues): self
    {
        $this->vues = $vues;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCours($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCours() === $this) {
                $like->setCours(null);
            }
        }

        return $this;
    }

    public function getMontantAbonnement(): ?int
    {
        return $this->montantAbonnement;
    }

    public function setMontantAbonnement(?int $montantAbonnement): self
    {
        $this->montantAbonnement = $montantAbonnement;

        return $this;
    }

    public function getProf(): ?string
    {
        return $this->enseignant->getUtilisateur()->getPersonne()->getName();
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): self
    {
        // set the owning side of the relation if necessary
        if ($media->getCours() !== $this) {
            $media->setCours($this);
        }

        $this->media = $media;

        return $this;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public Function setForum(Forum $forum): self
    {
        if ($forum->getCours() !== $this) {
            $forum->setCours($this);
        }

        $this->forum = $forum;

        return $this;
    }

    /**
     * @return Collection<int, FAQ>
     */
    public function getFAQs(): Collection
    {
        return $this->fAQs;
    }

    public function addFAQ(FAQ $fAQ): self
    {
        if (!$this->fAQs->contains($fAQ)) {
            $this->fAQs->add($fAQ);
            $fAQ->setCours($this);
        }

        return $this;
    }

    public function removeFAQ(FAQ $fAQ): self
    {
        if ($this->fAQs->removeElement($fAQ)) {
            // set the owning side to null (unless already changed)
            if ($fAQ->getCours() === $this) {
                $fAQ->setCours(null);
            }
        }

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getNumberOfLessons(): ?int
    {
        return $this->numberOfLessons;
    }

    public function setNumberOfLessons(int $numberOfLessons): self
    {
        $this->numberOfLessons = $numberOfLessons;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function isIsRejected(): ?bool
    {
        return $this->isRejected;
    }

    public function setIsRejected(?bool $isRejected): self
    {
        $this->isRejected = $isRejected;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCours($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCours() === $this) {
                $review->setCours(null);
            }
        }

        return $this;
    }

    public function getReview(): ?int
    {
        return $this->review;
    }

    public function setReview(?int $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setCours($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCours() === $this) {
                $payment->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentMethod>
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): self
    {
        if (!$this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->add($paymentMethod);
        }

        return $this;
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethods->removeElement($paymentMethod);

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
            $lecture->setCours($this);
        }

        return $this;
    }

    public function removeLecture(Lecture $lecture): self
    {
        if ($this->lectures->removeElement($lecture)) {
            // set the owning side to null (unless already changed)
            if ($lecture->getCours() === $this) {
                $lecture->setCours(null);
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
            $quiz->setCours($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getCours() === $this) {
                $quiz->setCours(null);
            }
        }

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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
            $quizLost->setCours($this);
        }

        return $this;
    }

    public function removeQuizLost(QuizLost $quizLost): self
    {
        if ($this->quizLosts->removeElement($quizLost)) {
            // set the owning side to null (unless already changed)
            if ($quizLost->getCours() === $this) {
                $quizLost->setCours(null);
            }
        }

        return $this;
    }

    public function getNumberOfStudents(): int
    {
        return $this->getEleves()->count();
    }

    public function getSkillLevel(): ?SkillLevel
    {
        return $this->skillLevel;
    }

    public function setSkillLevel(?SkillLevel $skillLevel): self
    {
        $this->skillLevel = $skillLevel;

        return $this;
    }
}
