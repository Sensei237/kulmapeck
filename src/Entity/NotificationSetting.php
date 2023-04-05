<?php

namespace App\Entity;

use App\Repository\NotificationSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationSettingRepository::class)]
class NotificationSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notificationSettings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notificationSettings')]
    private ?NotificationType $notificationType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNotificationType(): ?NotificationType
    {
        return $this->notificationType;
    }

    public function setNotificationType(?NotificationType $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }
}
