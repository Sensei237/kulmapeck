<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:abonnement:collection', 'read:abonnement:item']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:abonnement:collection']],
            order: ['montant' => 'ASC']
        )
    ],
    paginationItemsPerPage: 50,
    paginationMaximumItemsPerPage: 50,
    normalizationContext: [
        'groups' => ['read:abonnement:collection']
    ],
)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?string $slug = null;

    #[ORM\Column]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?float $montant = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?int $duree = null;

    #[ORM\OneToMany(mappedBy: 'abonnement', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\Column]
    #[Groups(['read:abonnement:collection', 'read:payment:collection'])]
    private ?bool $isRecommended = false;

    #[ORM\ManyToMany(targetEntity: AbonnementItem::class, inversedBy: 'abonnements')]
    #[Groups(['read:abonnement:collection'])]
    private Collection $items;

    #[ORM\ManyToMany(targetEntity: PaymentMethod::class, inversedBy: 'abonnements')]
    #[Groups(['read:payment:collection'])]
    private Collection $paymentMethods;

    #[ORM\Column(nullable: true)]
    private ?int $NbrePoint = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

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
            $payment->setAbonnement($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getAbonnement() === $this) {
                $payment->setAbonnement(null);
            }
        }

        return $this;
    }

    public function isIsRecommended(): ?bool
    {
        return $this->isRecommended;
    }

    public function setIsRecommended(bool $isRecommended): self
    {
        $this->isRecommended = $isRecommended;

        return $this;
    }

    /**
     * @return Collection<int, AbonnementItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(AbonnementItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(AbonnementItem $item): self
    {
        $this->items->removeElement($item);

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

    public function getNbrePoint(): ?int
    {
        return $this->NbrePoint;
    }

    public function setNbrePoint(?int $NbrePoint): static
    {
        $this->NbrePoint = $NbrePoint;

        return $this;
    }
}
