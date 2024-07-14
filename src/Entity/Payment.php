<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\Api\Controller\Payment\PayerAbonnementController;
use App\Controller\Api\Controller\Payment\PayerCoursController;
use App\Controller\Api\Controller\Payment\StudentPaymentController;
use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/student/{id}/payments',
            controller: StudentPaymentController::class,
            read: false,
            openapiContext: [
                'security' => [['bearerAuth' => []]]
            ]
        ),
        new Post(
            uriTemplate: '/abonnement/{id}/subscribe',
            controller: PayerAbonnementController::class,
            read: false,
            write: false,
            openapi: new Operation(
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'payment_method' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ])
                ),
                security: [['bearerAuth' => []]],
                description: "Cette route permet à un élève de payer un abonnement. Ici ID = id de l'abonnement. Dans le corps de la requête HTTP, il faut envoyé la propriété payment_method contenant la methode de paiement et initiate_payment qui est juste un booleen",
                summary: "Cette route permet à un élève de payer un abonnement. Ici ID = id de l'abonnement. Dans le corps de la requête HTTP, il faut envoyé la propriété payment_method contenant la methode de paiement et initiate_payment qui est juste un booleen"
            ),
        ),
        new Post(
            uriTemplate: '/cours/{id}/paied',
            controller: PayerCoursController::class,
            read: false,
            write: false,
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                'description' => "Cette route permet à un élève de payer un cours. Ici ID = id du cours. Dans le corps de la requête HTTP, il faut envoyé la propriété payment_method contenant la methode de paiement"
            ]
        ),
    ],
    normalizationContext: [
        'groups' => ['read:payment:collection']
    ]
)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:payment:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[Groups(['read:payment:collection'])]
    private ?Abonnement $abonnement = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[Groups(['read:payment:collection'])]
    private ?Cours $cours = null;

    #[ORM\Column]
    #[Groups(['read:payment:collection'])]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column]
    #[Groups(['read:payment:collection'])]
    private ?bool $isExpired = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:payment:collection'])]
    private ?\DateTimeImmutable $expiredAt = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:payment:collection'])]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\Column(length: 20)]
    #[Groups(['read:payment:collection'])]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:payment:collection'])]
    private ?float $amount = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['read:payment:collection'])]
    private ?string $status = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(['read:payment:collection'])]
    private ?string $transactionReference = null;

    public function __construct()
    {
        $this->paidAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;

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

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function isIsExpired(): ?bool
    {
        return $this->isExpired;
    }

    public function setIsExpired(bool $isExpired): self
    {
        $this->isExpired = $isExpired;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(?\DateTimeImmutable $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(?string $transactionReference): static
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }
}
