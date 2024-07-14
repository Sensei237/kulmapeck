<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\Controller\User\MeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Valid;

#[ ORM\Entity( repositoryClass: UserRepository::class ) ]
#[ ORM\Table( name: '`kulmapeck_user`' ) ]
#[ UniqueEntity( fields: [ 'email' ], message: 'There is already an account with this email' ) ]
#[ ORM\EntityListeners( [ 'App\EntityListener\UserListener' ] ) ]
#[ ORM\HasLifecycleCallbacks ]
#[ ApiResource(
    operations: [
        new Post(
            denormalizationContext: [
                'groups' => [ 'post:user:item' ]
            ],
        ),
        new Get(
            normalizationContext: [
                'groups' => [ 'post:user:item', 'read:user:item' ]
            ],
        ),
        new Get(
            paginationEnabled: false,
            uriTemplate: '/me',
            controller: MeController::class,
            read: false,
            normalizationContext: [
                'groups' => [ 'read:user:item', 'post:user:item', 'read:personne:item' ]
            ],
            openapiContext: [
                'security' => [ [ 'bearerAuth' => [] ] ]
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => [ 'post:user:item' ]
            ],
        ),
    ],
    normalizationContext: [
        'groups' => [ 'read:user:item' ]
    ],
) ]

class User implements UserInterface, PasswordAuthenticatedUserInterface
 {
    #[ ORM\Id ]
    #[ ORM\GeneratedValue ]
    #[ ORM\Column ]
    #[ Groups( [ 'read:course:collection', 'read:forum:item', 'read:forum:messsage:collection', 'read:sujet:item', 'read:sujet:collection', 'read:exam:collection', 'read:user:item', 'read:payment:collection', 'read:review:collection' ] ) ]
    private ?int $id = null;

    #[ ORM\Column( length: 180, unique: true ) ]
    #[ Assert\Email( message: "L'email {{ value }} n'est pas une adresse e-mail valide !" ) ]
    #[ Groups( [ 'read:course:collection', 'read:sujet:collection', 'post:user:item', 'post:user:item', 'read:payment:collection', 'read:review:collection' ] ) ]
    private ?string $email = null;

    #[ ORM\Column ]
    #[ Groups( [ 'read:user:item' ] ) ]
    private array $roles = [];

    /**
    * @var string The hashed password
    */
    #[ ORM\Column ]
    #[ Groups( [ 'post:user:item' ] ) ]
    private ?string $password = null;

    #[ ORM\OneToMany( mappedBy: 'destinataire', targetEntity: Notification::class, orphanRemoval: true ) ]
    private Collection $notifications;

    #[ ORM\OneToOne( mappedBy: 'utilisateur', cascade: [ 'persist', 'remove' ] ) ]
    #[
        Groups( [ 'read:course:collection', 'post:user:item' ] ),
        Valid()
    ]
    private ?Eleve $eleve = null;

    #[ ORM\OneToOne( mappedBy: 'utilisateur', cascade: [ 'persist', 'remove' ] ) ]
    private ?Enseignant $enseignant = null;

    #[ ORM\OneToMany( mappedBy: 'author', targetEntity: Like::class, orphanRemoval: true ) ]
    private Collection $likes;

    #[ ORM\Column( type: 'boolean' ) ]
    private ?bool $isVerified = false;

    #[ Groups( [ 'post:user:item' ] ) ]
    public ?string $parentCode = null;

    #[ ORM\OneToOne( mappedBy: 'utilisateur', cascade: [ 'persist', 'remove' ] ) ]
    #[
        Groups( [ 'read:course:collection', 'read:personne:item', 'read:sujet:item', 'read:sujet:collection', 'read:review:collection', 'read:exam:collection', 'post:user:item', 'read:payment:collection', 'read:quizresult:collection' ] ),
        Valid()
    ]
    private ?Personne $personne = null;

    #[ ORM\Column( nullable: true ) ]
    #[ Groups( [ 'read:user:item', 'read:review:collection' ] ) ]
    private ?bool $isBlocked = null;

    #[ ORM\Column( nullable: true ) ]
    #[ Groups( [ 'read:user:item', 'read:review:collection' ] ) ]
    private ?bool $isAdmin = null;

    #[ ORM\OneToMany( mappedBy: 'user', targetEntity: Exam::class ) ]
    private Collection $exams;

    #[ ORM\OneToMany( mappedBy: 'user', targetEntity: NotificationSetting::class, orphanRemoval: true ) ]
    private Collection $notificationSettings;

    #[ ORM\ManyToMany( targetEntity: Filiere::class, inversedBy: 'users' ) ]
    private Collection $filieres;

    #[ ORM\Column( nullable: true ) ]
    #[ Groups( [ 'read:user:item' ] ) ]

    private ?float $points = null;

    #[ ORM\OneToMany( mappedBy: 'user', targetEntity: Retrait::class, orphanRemoval: true ) ]
    #[ Groups( [ 'read:user:item', 'read:retraits:collection' ] ) ]

    private Collection $retraits;

    #[ ORM\Column ]
    #[ Groups( [ 'read:user:item' ] ) ]

    private ?float $especes = null;

    #[ ORM\OneToMany( mappedBy: 'deviceToken', targetEntity: Device::class ) ]
    private Collection $devices;

    #[ ORM\OneToMany( mappedBy: 'user', targetEntity: Contact::class ) ]
    private Collection $contacts;

    public function __construct()
 {
        $this->notifications = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->notificationSettings = new ArrayCollection();
        $this->filieres = new ArrayCollection();
        $this->retraits = new ArrayCollection();
        $this->especes = 0;
        $this->points = 0;
        $this->isBlocked = false;
        $this->isVerified = false;
        $this->isAdmin = false;
        $this->devices = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getId(): ?int
 {
        return $this->id;
    }

    public function getEmail(): ?string
 {
        return $this->email;
    }

    public function setEmail( string $email ): self
 {
        $this->email = $email;

        return $this;
    }

    /**
    * A visual identifier that represents this user.
    *
    * @see UserInterface
    */

    public function getUserIdentifier(): string
 {
        return ( string ) $this->email;
    }

    /**
    * @see UserInterface
    */

    public function getRoles(): array
 {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique( $roles );
    }

    public function setRoles( array $roles ): self
 {
        $this->roles = $roles;

        return $this;
    }

    public function addRole( string $role ): self
 {
        $this->roles[] = $role;

        return $this;
    }

    /**
    * @see PasswordAuthenticatedUserInterface
    */

    public function getPassword(): string
 {
        return $this->password;
    }

    public function setPassword( string $password ): self
 {
        $this->password = $password;

        return $this;
    }

    /**
    * @see UserInterface
    */

    public function eraseCredentials()
 {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
    * @return Collection<int, Notification>
    */

    public function getNotifications(): Collection
 {
        return $this->notifications;
    }

    public function addNotification( Notification $notification ): self
 {
        if ( !$this->notifications->contains( $notification ) ) {
            $this->notifications->add( $notification );
            $notification->setDestinataire( $this );
        }

        return $this;
    }

    public function removeNotification( Notification $notification ): self
 {
        if ( $this->notifications->removeElement( $notification ) ) {
            // set the owning side to null ( unless already changed )
            if ( $notification->getDestinataire() === $this ) {
                $notification->setDestinataire( null );
            }
        }

        return $this;
    }

    public function getEleve(): ?Eleve
 {
        return $this->eleve;
    }

    public function setEleve( Eleve $eleve ): self
 {
        // set the owning side of the relation if necessary
        if ( $eleve->getUtilisateur() !== $this ) {
            $eleve->setUtilisateur( $this );
        }

        $this->eleve = $eleve;

        return $this;
    }

    public function getEnseignant(): ?Enseignant
 {
        return $this->enseignant;
    }

    public function setEnseignant( Enseignant $enseignant ): self
 {
        // set the owning side of the relation if necessary
        if ( $enseignant->getUtilisateur() !== $this ) {
            $enseignant->setUtilisateur( $this );
        }

        $this->enseignant = $enseignant;

        return $this;
    }

    /**
    * @return Collection<int, Like>
    */

    public function getLikes(): Collection
 {
        return $this->likes;
    }

    public function addLike( Like $like ): self
 {
        if ( !$this->likes->contains( $like ) ) {
            $this->likes->add( $like );
            $like->setAuthor( $this );
        }

        return $this;
    }

    public function removeLike( Like $like ): self
 {
        if ( $this->likes->removeElement( $like ) ) {
            // set the owning side to null ( unless already changed )
            if ( $like->getAuthor() === $this ) {
                $like->setAuthor( null );
            }
        }

        return $this;
    }

    public function isVerified(): bool
 {
        return $this->isVerified;
    }

    public function setIsVerified( bool $isVerified ): self
 {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPersonne(): ?Personne
 {
        return $this->personne;
    }

    public function setPersonne( ?Personne $personne ): self
 {
        $this->personne = $personne;

        return $this;
    }

    public function isIsBlocked(): ?bool
 {
        return $this->isBlocked;
    }

    public function setIsBlocked( ?bool $isBlocked ): self
 {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function isIsAdmin(): ?bool
 {
        return $this->isAdmin;
    }

    public function setIsAdmin( ?bool $isAdmin ): self
 {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
    * @return Collection<int, Exam>
    */

    public function getExams(): Collection
 {
        return $this->exams;
    }

    public function addExam( Exam $exam ): self
 {
        if ( !$this->exams->contains( $exam ) ) {
            $this->exams->add( $exam );
            $exam->setUser( $this );
        }

        return $this;
    }

    public function removeExam( Exam $exam ): self
 {
        if ( $this->exams->removeElement( $exam ) ) {
            // set the owning side to null ( unless already changed )
            if ( $exam->getUser() === $this ) {
                $exam->setUser( null );
            }
        }

        return $this;
    }

    /**
    * @return Collection<int, NotificationSetting>
    */

    public function getNotificationSettings(): Collection
 {
        return $this->notificationSettings;
    }

    public function addNotificationSetting( NotificationSetting $notificationSetting ): self
 {
        if ( !$this->notificationSettings->contains( $notificationSetting ) ) {
            $this->notificationSettings->add( $notificationSetting );
            $notificationSetting->setUser( $this );
        }

        return $this;
    }

    public function removeNotificationSetting( NotificationSetting $notificationSetting ): self
 {
        if ( $this->notificationSettings->removeElement( $notificationSetting ) ) {
            // set the owning side to null ( unless already changed )
            if ( $notificationSetting->getUser() === $this ) {
                $notificationSetting->setUser( null );
            }
        }

        return $this;
    }

    /**
    * @return Collection<int, Filiere>
    */

    public function getFilieres(): Collection
 {
        return $this->filieres;
    }

    public function addFiliere( Filiere $filiere ): self
 {
        if ( !$this->filieres->contains( $filiere ) ) {
            $this->filieres->add( $filiere );
        }

        return $this;
    }

    public function removeFiliere( Filiere $filiere ): self
 {
        $this->filieres->removeElement( $filiere );

        return $this;
    }

    public function getPoints(): ?float
 {
        return $this->points;
    }

    public function setPoints( ?float $points ): static
 {
        $this->points = $points;

        return $this;
    }

    /**
    * @return Collection<int, Retrait>
    */

    public function getRetraits(): Collection
 {
        return $this->retraits;
    }

    public function addRetrait( Retrait $retrait ): static
 {
        if ( !$this->retraits->contains( $retrait ) ) {
            $this->retraits->add( $retrait );
            $retrait->setUser( $this );
        }

        return $this;
    }

    public function removeRetrait( Retrait $retrait ): static
 {
        if ( $this->retraits->removeElement( $retrait ) ) {
            // set the owning side to null ( unless already changed )
            if ( $retrait->getUser() === $this ) {
                $retrait->setUser( null );
            }
        }

        return $this;
    }

    public function getEspeces(): ?float
 {
        return $this->especes;
    }

    public function setEspeces( float $especes ): static
 {
        $this->especes = $especes;

        return $this;
    }

    /**
    * @return Collection<int, Device>
    */

    public function getDevices(): Collection
 {
        return $this->devices;
    }

    public function addDevice( Device $device ): static
 {
        if ( !$this->devices->contains( $device ) ) {
            $this->devices->add( $device );
            $device->setDeviceToken( $this );
        }

        return $this;
    }

    public function removeDevice( Device $device ): static
 {
        if ( $this->devices->removeElement( $device ) ) {
            // set the owning side to null ( unless already changed )
            if ( $device->getDeviceToken() === $this ) {
                $device->setDeviceToken( null );
            }
        }

        return $this;
    }

    /**
    * @return Collection<int, Contact>
    */

    public function getContacts(): Collection
 {
        return $this->contacts;
    }

    public function addContact( Contact $contact ): static
 {
        if ( !$this->contacts->contains( $contact ) ) {
            $this->contacts->add( $contact );
            $contact->setUser( $this );
        }

        return $this;
    }

    public function removeContact( Contact $contact ): static
 {
        if ( $this->contacts->removeElement( $contact ) ) {
            // set the owning side to null ( unless already changed )
            if ( $contact->getUser() === $this ) {
                $contact->setUser( null );
            }
        }

        return $this;
    }

    public function __toString()
 {
        return $this->getPersonne()->getNomComplet();
        // Or any other property that represents the user as a string
    }

}
