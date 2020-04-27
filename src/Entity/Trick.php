<?php

declare(strict_types=1);

namespace App\Entity;

use App\CustomServices\SlugMaker;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 * @UniqueEntity(fields={"name"}, message="There is already a trick with this name")
 * @UniqueEntity(fields={"slug"}, message="There is already a trick with this slug")
 */
class Trick
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
    private ?string $name = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTimeInterface $dateAdded;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $dateModified;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
     */
    private ?User $author = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", inversedBy="tricks")
     */
    private ?Collection $medias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Media", inversedBy="tricksMainImages")
     */
    private ?Media $mainImage = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", orphanRemoval=true)
     */
    private ?Collection $comments;

    /**
     * @ORM\ManyToOne(targetEntity="TrickGroup", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?TrickGroup $trickGroup = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $status = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug = null;

    private SlugMaker $slugMaker;


    /**
     * Trick constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateAdded(): ?DateTimeInterface
    {
        return $this->dateAdded;
    }

    /**
     * @param DateTimeInterface|null $dateAdded
     * @return $this
     */
    public function setDateAdded(?DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateModified(): ?DateTimeInterface
    {
        return $this->dateModified;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateModified(): self
    {
        $this->dateModified = new DateTime("now");

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }

        return $this;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function removeMedia(Media $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    /**
     * @return Media|null
     */
    public function getMainImage(): ?Media
    {
        return $this->mainImage;
    }

    /**
     * @param Media|null $mainImage
     * @return $this
     */
    public function setMainImage(?Media $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return TrickGroup|null
     */
    public function getTrickGroup(): ?TrickGroup
    {
        return $this->trickGroup;
    }

    /**
     * @param TrickGroup|null $trickGroup
     * @return $this
     */
    public function setTrickGroup(?TrickGroup $trickGroup): self
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * Sets the online status: false = offline, true = online
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status = false): self
    {
        $this->status = $status;

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
