<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:course:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?string $videoUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?string $mp4File = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?string $webMFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:course:collection'])]
    private ?string $oggFile = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:course:collection'])]
    private ?string $imageFile = null;
    
    #[ORM\OneToOne(inversedBy: 'media', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;


    /**
     * @var File
     */
    public $mp4FileUpload;

    /**
     * @var File
     */
    public $imageFileUpload;

    /**
     * @var File
     */
    public $oggFileUpload;

    /**
     * @var File
     */
    public $webMFileUpload;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getMp4File(): ?string
    {
        return $this->mp4File;
    }

    public function setMp4File(?string $mp4File): self
    {
        $this->mp4File = $mp4File;

        return $this;
    }

    public function getWebMFile(): ?string
    {
        return $this->webMFile;
    }

    public function setWebMFile(?string $webMFile): self
    {
        $this->webMFile = $webMFile;

        return $this;
    }

    public function getOggFile(): ?string
    {
        return $this->oggFile;
    }

    public function setOggFile(?string $oggFile): self
    {
        $this->oggFile = $oggFile;

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }
}
