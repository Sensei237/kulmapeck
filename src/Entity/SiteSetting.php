<?php

namespace App\Entity;

use App\Repository\SiteSettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingRepository::class)]
class SiteSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $siteName = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $siteCopyrights = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $siteEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $siteDescription = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $supportEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contactAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainSiteUrl = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isMaintenanceMode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $maintenanceText = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getSiteCopyrights(): ?string
    {
        return $this->siteCopyrights;
    }

    public function setSiteCopyrights(?string $siteCopyrights): self
    {
        $this->siteCopyrights = $siteCopyrights;

        return $this;
    }

    public function getSiteEmail(): ?string
    {
        return $this->siteEmail;
    }

    public function setSiteEmail(string $siteEmail): self
    {
        $this->siteEmail = $siteEmail;

        return $this;
    }

    public function getSiteDescription(): ?string
    {
        return $this->siteDescription;
    }

    public function setSiteDescription(string $siteDescription): self
    {
        $this->siteDescription = $siteDescription;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(string $supportEmail): self
    {
        $this->supportEmail = $supportEmail;

        return $this;
    }

    public function getContactAddress(): ?string
    {
        return $this->contactAddress;
    }

    public function setContactAddress(string $contactAddress): self
    {
        $this->contactAddress = $contactAddress;

        return $this;
    }

    public function getMainSiteUrl(): ?string
    {
        return $this->mainSiteUrl;
    }

    public function setMainSiteUrl(string $mainSiteUrl): self
    {
        $this->mainSiteUrl = $mainSiteUrl;

        return $this;
    }

    public function isIsMaintenanceMode(): ?bool
    {
        return $this->isMaintenanceMode;
    }

    public function setIsMaintenanceMode(?bool $isMaintenanceMode): self
    {
        $this->isMaintenanceMode = $isMaintenanceMode;

        return $this;
    }

    public function getMaintenanceText(): ?string
    {
        return $this->maintenanceText;
    }

    public function setMaintenanceText(?string $maintenanceText): self
    {
        $this->maintenanceText = $maintenanceText;

        return $this;
    }
}
