<?php

declare(strict_types=1);

namespace App\Entity;

use App\CustomServices\Removable;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media implements Removable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $fileName = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $mimeType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $alt = null;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTimeInterface $dateAdded;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trick", mappedBy="medias")
     */
    private ?Collection $tricks = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="mainImage")
     */
    private ?Collection $tricksMainImages = null;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->tricksMainImages = new ArrayCollection();
        $this->dateAdded = new DateTime("now");
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     * @return $this
     */
    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string|null $alt
     * @return $this
     */
    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateAdded(): DateTimeInterface
    {
        return $this->dateAdded;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): ?Collection
    {
        return $this->tricks;
    }

    /**
     * @param Trick $trick
     * @return $this
     */
    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->addMedia($this);
        }

        return $this;
    }

    /**
     * @param Trick $trick
     * @return $this
     */
    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            $trick->removeMedia($this);
        }

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricksMainImages(): ?Collection
    {
        return $this->tricksMainImages;
    }

    /**
     * @param Trick $tricksMainImage
     * @return $this
     */
    public function addTricksMainImage(Trick $tricksMainImage): self
    {
        if (!$this->tricksMainImages->contains($tricksMainImage)) {
            $this->tricksMainImages[] = $tricksMainImage;
            $tricksMainImage->setMainImage($this);
        }

        return $this;
    }

    /**
     * @param Trick $tricksMainImage
     * @return $this
     */
    public function removeTricksMainImage(Trick $tricksMainImage): self
    {
        if ($this->tricksMainImages->contains($tricksMainImage)) {
            $this->tricksMainImages->removeElement($tricksMainImage);
            // set the owning side to null (unless already changed)
            if ($tricksMainImage->getMainImage() === $this) {
                $tricksMainImage->setMainImage(null);
            }
        }

        return $this;
    }
}
