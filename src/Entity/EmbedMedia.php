<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmbedMediaRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=EmbedMediaRepository::class)
 */
class EmbedMedia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $htmlCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaName;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTimeInterface $dateAdded;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="embedMedias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * Trick constructor.
     * @throws Exception
     */
    public function __construct()
    {
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
    public function getHtmlCode(): ?string
    {
        return $this->htmlCode;
    }

    /**
     * @param string $htmlCode
     * @return $this
     */
    public function setHtmlCode(string $htmlCode): self
    {
        $this->htmlCode = $htmlCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }

    /**
     * @param string|null $mediaName
     * @return $this
     */
    public function setMediaName(?string $mediaName): self
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    /**
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    /**
     * @param Trick|null $trick
     * @return $this
     */
    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
