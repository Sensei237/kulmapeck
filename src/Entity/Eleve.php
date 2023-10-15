<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ ORM\Entity( repositoryClass: EleveRepository::class ) ]

class Eleve
 {
    #[ ORM\Id ]
    #[ ORM\GeneratedValue ]
    #[ ORM\Column ]
    #[ Groups( [ 'read:course:item', 'read:user:item', 'read:quizresult:collection', 'read:review:collection' ] ) ]
    private ?int $id = null;

    #[ ORM\ManyToOne( inversedBy: 'eleves' ) ]
    #[ Groups( [ 'post:user:item' ] ) ]
    private ?Classe $classe = null;

    #[ ORM\ManyToMany( targetEntity: Cours::class, inversedBy: 'eleves' ) ]
    private Collection $cours;

    #[ ORM\ManyToMany( targetEntity: Formation::class, mappedBy: 'eleves' ) ]
    private Collection $formations;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: Reponse::class, orphanRemoval: true ) ]
    private Collection $reponses;

    #[ ORM\OneToOne( inversedBy: 'eleve', cascade: [ 'persist', 'remove' ] ) ]
    #[ ORM\JoinColumn( nullable: false ) ]
    #[ Groups( [ 'read:course:item', 'read:quizresult:collection', 'read:review:collection' ] ) ]
    private ?User $utilisateur = null;

    #[ ORM\ManyToOne( inversedBy: 'eleves' ) ]
    #[ ORM\JoinColumn( nullable: true ) ]
    #[ Groups( [ 'post:user:item' ] ) ]
    private ?Etablissement $etablissement = null;

    #[ ORM\Column( length: 100, unique: true ) ]
    #[ Groups( [ 'read:user:item', 'read:review:collection' ] ) ]
    private ?string $reference = null;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: Review::class ) ]
    private Collection $reviews;

    #[ ORM\Column ]
    #[ Groups( [ 'read:user:item', 'read:review:collection' ] ) ]
    private ?\DateTimeImmutable $joinAt = null;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: Payment::class, orphanRemoval: true ) ]
    private Collection $payments;

    #[ ORM\Column( nullable: true ) ]
    #[ Groups( [ 'read:user:item', 'read:review:collection' ] ) ]
    private ?bool $isPremium = null;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: Lecture::class, orphanRemoval: true ) ]
    private Collection $lectures;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: QuizResult::class, orphanRemoval: true ) ]
    private Collection $quizResults;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: QuizLost::class, orphanRemoval: true ) ]
    private Collection $quizLosts;

    #[ ORM\ManyToMany( targetEntity: Evaluation::class, mappedBy: 'Eleves' ) ]
    private Collection $evaluations;

    #[ ORM\OneToMany( mappedBy: 'eleve', targetEntity: EvaluationResultat::class, orphanRemoval: true ) ]
    private Collection $evaluationResultats;

    public function __construct()
 {
        $this->cours = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->joinAt = new DateTimeImmutable();
        $this->payments = new ArrayCollection();
        $this->lectures = new ArrayCollection();
        $this->quizResults = new ArrayCollection();
        $this->isPremium = false;
        $this->quizLosts = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->evaluationResultats = new ArrayCollection();
    }

    public function getId(): ?int
 {
        return $this->id;
    }

    public function getClasse(): ?Classe
 {
        return $this->classe;
    }

    public function setClasse( ?Classe $classe ): self
 {
        $this->classe = $classe;

        return $this;
    }

    /**
    * @return Collection<int, Cours>
    */

    public function getCours(): Collection
 {
        return $this->cours;
    }

    public function addCour( Cours $cour ): self
 {
        if ( !$this->cours->contains( $cour ) ) {
            $this->cours->add( $cour );
        }

        return $this;
    }

    public function removeCour( Cours $cour ): self
 {
        $this->cours->removeElement( $cour );

        return $this;
    }

    /**
    * @return Collection<int, Formation>
    */

    public function getFormations(): Collection
 {
        return $this->formations;
    }

    public function addFormation( Formation $formation ): self
 {
        if ( !$this->formations->contains( $formation ) ) {
            $this->formations->add( $formation );
            $formation->addElefe( $this );
        }

        return $this;
    }

    public function removeFormation( Formation $formation ): self
 {
        if ( $this->formations->removeElement( $formation ) ) {
            $formation->removeElefe( $this );
        }

        return $this;
    }

    /**
    * @return Collection<int, Reponse>
    */

    public function getReponses(): Collection
 {
        return $this->reponses;
    }

    public function addReponse( Reponse $reponse ): self
 {
        if ( !$this->reponses->contains( $reponse ) ) {
            $this->reponses->add( $reponse );
            $reponse->setEleve( $this );
        }

        return $this;
    }

    public function removeReponse( Reponse $reponse ): self
 {
        if ( $this->reponses->removeElement( $reponse ) ) {
            // set the owning side to null ( unless already changed )
            if ( $reponse->getEleve() === $this ) {
                $reponse->setEleve( null );
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?User
 {
        return $this->utilisateur;
    }

    public function setUtilisateur( User $utilisateur ): self
 {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getEtablissement(): ?Etablissement
 {
        return $this->etablissement;
    }

    public function setEtablissement( ?Etablissement $etablissement ): self
 {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getReference(): ?string
 {
        return $this->reference;
    }

    public function setReference( string $reference ): self
 {
        $this->reference = $reference;

        return $this;
    }

    /**
    * @return Collection<int, Review>
    */

    public function getReviews(): Collection
 {
        return $this->reviews;
    }

    public function addReview( Review $review ): self
 {
        if ( !$this->reviews->contains( $review ) ) {
            $this->reviews->add( $review );
            $review->setEleve( $this );
        }

        return $this;
    }

    public function removeReview( Review $review ): self
 {
        if ( $this->reviews->removeElement( $review ) ) {
            // set the owning side to null ( unless already changed )
            if ( $review->getEleve() === $this ) {
                $review->setEleve( null );
            }
        }

        return $this;
    }

    public function getJoinAt(): ?\DateTimeImmutable
 {
        return $this->joinAt;
    }

    public function setJoinAt( \DateTimeImmutable $joinAt ): self
 {
        $this->joinAt = $joinAt;

        return $this;
    }

    /**
    * @return Collection<int, Payment>
    */

    public function getPayments(): Collection
 {
        return $this->payments;
    }

    public function addPayment( Payment $payment ): self
 {
        if ( !$this->payments->contains( $payment ) ) {
            $this->payments->add( $payment );
            $payment->setEleve( $this );
        }

        return $this;
    }

    public function removePayment( Payment $payment ): self
 {
        if ( $this->payments->removeElement( $payment ) ) {
            // set the owning side to null ( unless already changed )
            if ( $payment->getEleve() === $this ) {
                $payment->setEleve( null );
            }
        }

        return $this;
    }

    public function isIsPremium(): ?bool
 {
        return $this->isPremium;
    }

    public function setIsPremium( ?bool $isPremium ): self
 {
        $this->isPremium = $isPremium;

        return $this;
    }

    /**
    * @return Collection<int, Lecture>
    */

    public function getLectures(): Collection
 {
        return $this->lectures;
    }

    public function addLecture( Lecture $lecture ): self
 {
        if ( !$this->lectures->contains( $lecture ) ) {
            $this->lectures->add( $lecture );
            $lecture->setEleve( $this );
        }

        return $this;
    }

    public function removeLecture( Lecture $lecture ): self
 {
        if ( $this->lectures->removeElement( $lecture ) ) {
            // set the owning side to null ( unless already changed )
            if ( $lecture->getEleve() === $this ) {
                $lecture->setEleve( null );
            }
        }

        return $this;
    }

    /**
    * @return Collection<int, QuizResult>
    */

    public function getQuizResults(): Collection
 {
        return $this->quizResults;
    }

    public function addQuizResult( QuizResult $quizResult ): self
 {
        if ( !$this->quizResults->contains( $quizResult ) ) {
            $this->quizResults->add( $quizResult );
            $quizResult->setEleve( $this );
        }

        return $this;
    }

    public function removeQuizResult( QuizResult $quizResult ): self
 {
        if ( $this->quizResults->removeElement( $quizResult ) ) {
            // set the owning side to null ( unless already changed )
            if ( $quizResult->getEleve() === $this ) {
                $quizResult->setEleve( null );
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

    public function addQuizLost( QuizLost $quizLost ): self
 {
        if ( !$this->quizLosts->contains( $quizLost ) ) {
            $this->quizLosts->add( $quizLost );
            $quizLost->setEleve( $this );
        }

        return $this;
    }

    public function removeQuizLost( QuizLost $quizLost ): self
 {
        if ( $this->quizLosts->removeElement( $quizLost ) ) {
            // set the owning side to null ( unless already changed )
            if ( $quizLost->getEleve() === $this ) {
                $quizLost->setEleve( null );
            }
        }

        return $this;
    }

    /**
    * @return Collection<int, Evaluation>
    */

    public function getEvaluations(): Collection
 {
        return $this->evaluations;
    }

    public function addEvaluation( Evaluation $evaluation ): self
 {
        if ( !$this->evaluations->contains( $evaluation ) ) {
            $this->evaluations->add( $evaluation );
            $evaluation->addEleve( $this );
        }

        return $this;
    }

    public function removeEvaluation( Evaluation $evaluation ): self
 {
        if ( $this->evaluations->removeElement( $evaluation ) ) {
            $evaluation->removeEleve( $this );
        }

        return $this;
    }

    /**
    * @return Collection<int, EvaluationResultat>
    */

    public function getEvaluationResultats(): Collection
 {
        return $this->evaluationResultats;
    }

    public function addEvaluationResultat( EvaluationResultat $evaluationResultat ): self
 {
        if ( !$this->evaluationResultats->contains( $evaluationResultat ) ) {
            $this->evaluationResultats->add( $evaluationResultat );
            $evaluationResultat->setEleve( $this );
        }

        return $this;
    }

    public function removeEvaluationResultat( EvaluationResultat $evaluationResultat ): self
 {
        if ( $this->evaluationResultats->removeElement( $evaluationResultat ) ) {
            // set the owning side to null ( unless already changed )
            if ( $evaluationResultat->getEleve() === $this ) {
                $evaluationResultat->setEleve( null );
            }
        }

        return $this;
    }
}
