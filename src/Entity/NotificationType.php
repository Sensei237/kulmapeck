<?php

namespace App\Entity;

use App\Repository\NotificationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationTypeRepository::class)]
class NotificationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'notificationType', targetEntity: NotificationSetting::class)]
    private Collection $notificationSettings;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\OneToOne(mappedBy: 'type', cascade: ['persist', 'remove'])]
    private ?NotificationTemplate $notificationTemplate = null;

    public function __construct()
    {
        $this->notificationSettings = new ArrayCollection();
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

    /**
     * @return Collection<int, NotificationSetting>
     */
    public function getNotificationSettings(): Collection
    {
        return $this->notificationSettings;
    }

    public function addNotificationSetting(NotificationSetting $notificationSetting): self
    {
        if (!$this->notificationSettings->contains($notificationSetting)) {
            $this->notificationSettings->add($notificationSetting);
            $notificationSetting->setNotificationType($this);
        }

        return $this;
    }

    public function removeNotificationSetting(NotificationSetting $notificationSetting): self
    {
        if ($this->notificationSettings->removeElement($notificationSetting)) {
            // set the owning side to null (unless already changed)
            if ($notificationSetting->getNotificationType() === $this) {
                $notificationSetting->setNotificationType(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNotificationTemplate(): ?NotificationTemplate
    {
        return $this->notificationTemplate;
    }

    public function setNotificationTemplate(NotificationTemplate $notificationTemplate): self
    {
        // set the owning side of the relation if necessary
        if ($notificationTemplate->getType() !== $this) {
            $notificationTemplate->setType($this);
        }

        $this->notificationTemplate = $notificationTemplate;

        return $this;
    }
}
