<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PaymentMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection()
    ],
    normalizationContext: [
        'groups' => ['read:payment_method:collection']
    ]
)]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:item', 'read:payment:collection', 'read:payment_method:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:item', 'read:payment:collection', 'read:payment_method:collection'])]
    private ?string $label = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read:course:item', 'read:payment:collection', 'read:payment_method:collection'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:item', 'read:payment:collection', 'read:payment_method:collection'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'paymentMethod', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\ManyToMany(targetEntity: Cours::class, mappedBy: 'paymentMethods')]
    private Collection $cours;

    #[ORM\ManyToMany(targetEntity: Abonnement::class, mappedBy: 'paymentMethods')]
    private Collection $abonnements;

    #[ORM\OneToMany(mappedBy: 'paymentMethod', targetEntity: Retrait::class, orphanRemoval: true)]
    private Collection $retraits;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
        $this->retraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
            $payment->setPaymentMethod($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getPaymentMethod() === $this) {
                $payment->setPaymentMethod(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->addPaymentMethod($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            $cour->removePaymentMethod($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->addPaymentMethod($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        if ($this->abonnements->removeElement($abonnement)) {
            $abonnement->removePaymentMethod($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Retrait>
     */
    public function getRetraits(): Collection
    {
        return $this->retraits;
    }

    public function addRetrait(Retrait $retrait): static
    {
        if (!$this->retraits->contains($retrait)) {
            $this->retraits->add($retrait);
            $retrait->setPaymentMethod($this);
        }

        return $this;
    }

    public function removeRetrait(Retrait $retrait): static
    {
        if ($this->retraits->removeElement($retrait)) {
            // set the owning side to null (unless already changed)
            if ($retrait->getPaymentMethod() === $this) {
                $retrait->setPaymentMethod(null);
            }
        }

        return $this;
    }
}
