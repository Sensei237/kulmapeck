<?php

namespace App\Entity;

use App\Repository\NetworkConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NetworkConfigRepository::class)]
class NetworkConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nombreDePointsParInvitaton = null;

    #[ORM\Column]
    private ?float $pourcentageDistributionEnseignant = null;

    #[ORM\Column]
    private ?float $pourcentageDistributionEleve = null;

    #[ORM\Column]
    private ?float $tauxDeChange = null;

    #[ORM\Column]
    private ?float $minimumRetirable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreDePointsParInvitaton(): ?int
    {
        return $this->nombreDePointsParInvitaton;
    }

    public function setNombreDePointsParInvitaton(int $NombreDePointsParInvitaton): static
    {
        $this->nombreDePointsParInvitaton = $NombreDePointsParInvitaton;

        return $this;
    }

    public function getPourcentageDistributionEnseignant(): ?float
    {
        return $this->pourcentageDistributionEnseignant;
    }

    public function setPourcentageDistributionEnseignant(float $pourcentageDistributionEnseignant): static
    {
        $this->pourcentageDistributionEnseignant = $pourcentageDistributionEnseignant;

        return $this;
    }

    public function getPourcentageDistributionEleve(): ?float
    {
        return $this->pourcentageDistributionEleve;
    }

    public function setPourcentageDistributionEleve(float $pourcentageDistributionEleve): static
    {
        $this->pourcentageDistributionEleve = $pourcentageDistributionEleve;

        return $this;
    }

    public function getTauxDeChange(): ?float
    {
        return $this->tauxDeChange;
    }

    public function setTauxDeChange(float $tauxDeChange): static
    {
        $this->tauxDeChange = $tauxDeChange;

        return $this;
    }

    public function getMinimumRetirable(): ?float
    {
        return $this->minimumRetirable;
    }

    public function setMinimumRetirable(float $minimumRetirable): static
    {
        $this->minimumRetirable = $minimumRetirable;

        return $this;
    }
}
