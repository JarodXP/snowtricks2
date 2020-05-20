<?php

declare(strict_types=1);

namespace App\Entity;

use App\CustomServices\Removable;
use App\CustomServices\SlugMaker;
use App\Repository\LegalPageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LegalPageRepository::class)
 */
class LegalPage implements Removable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug = null;

    private ?SlugMaker $slugMaker = null;


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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        $this->slug = $this->slugMaker->sluggify($name);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return $this
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return SlugMaker|null
     */
    public function getSlugMaker(): ?SlugMaker
    {
        return $this->slugMaker;
    }

    /**
     * @param SlugMaker $slugMaker
     */
    public function setSlugMaker(SlugMaker $slugMaker)
    {
        $this->slugMaker = $slugMaker;
    }
}
