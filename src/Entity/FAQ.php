<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\Controller\Faq\FaqCourseController;
use App\Repository\FAQRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FAQRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext:['groups' => ['read:faq:collection']],
            name: 'course_faq',
            uriTemplate: '/cours/{id}/faqs',
            controller: FaqCourseController::class,
        )
    ],
    normalizationContext: ['groups' => ['read:faq:collection']]
)]
class FAQ
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:item', 'read:faq:collection'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fAQs', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:course:item', 'read:faq:collection'])]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:course:item', 'read:faq:collection'])]
    private ?string $answer = null;

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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
