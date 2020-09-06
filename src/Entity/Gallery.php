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
     * @ORM\Column(type="string", length=255)
     * @ORM\JoinColumn(nullable=false)
     */
    private $Image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories", inversedBy="galleries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="uploads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploader;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="views")
     * @ORM\JoinTable(name="user_gallery_views")
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity=Reports::class, mappedBy="item")
     */
    private $reports;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->reports = new ArrayCollection();
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

    public function getUploader(): ?User
    {
        return $this->uploader;
    }

    public function setUploader(?User $uploader): self
    {
        $this->uploader = $uploader;

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

    /**
     * @return Collection|Reports[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Reports $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setItem($this);
        }

        return $this;
    }

    public function removeReport(Reports $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getItem() === $this) {
                $report->setItem(null);
            }
        }

        return $this;
    }
}
