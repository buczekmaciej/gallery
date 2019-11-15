<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="blob")
     */
    private $Image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories", inversedBy="galleries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $addedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="likes")
     * @ORM\JoinTable(name="user_gallery_likes")
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="collection")
     * @ORM\JoinTable(name="user_gallery_saves")
     */
    private $saves;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="views")
     * @ORM\JoinTable(name="user_gallery_views")
     */
    private $views;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->saves = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage()
    {
        return $this->Image;
    }

    public function setImage($Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSaves(): Collection
    {
        return $this->saves;
    }

    public function addSave(User $save): self
    {
        if (!$this->saves->contains($save)) {
            $this->saves[] = $save;
        }

        return $this;
    }

    public function removeSave(User $save): self
    {
        if ($this->saves->contains($save)) {
            $this->saves->removeElement($save);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(User $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
        }

        return $this;
    }

    public function removeView(User $view): self
    {
        if ($this->views->contains($view)) {
            $this->views->removeElement($view);
        }

        return $this;
    }
}
