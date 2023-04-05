<?php

namespace App\Entity;

use App\Repository\NotificationTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationTemplateRepository::class)]
class NotificationTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $template = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $notificationType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getNotificationType(): ?int
    {
        return $this->notificationType;
    }

    public function setNotificationType(int $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }
}
