<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $Login;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $Password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $resetHash;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gallery", mappedBy="saves")
     */
    private $collection;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $colorSchema;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDisabled;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gallery", mappedBy="likes")
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gallery", mappedBy="views")
     */
    private $views;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->Login;
    }

    public function setLogin(string $Login): self
    {
        $this->Login = $Login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getResetHash(): ?string
    {
        return $this->resetHash;
    }

    public function setResetHash(string $resetHash): self
    {
        $this->resetHash = $resetHash;

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function addCollection(Gallery $collection): self
    {
        if (!$this->collection->contains($collection)) {
            $this->collection[] = $collection;
        }

        return $this;
    }

    public function removeCollection(Gallery $collection): self
    {
        if ($this->collection->contains($collection)) {
            $this->collection->removeElement($collection);
        }

        return $this;
    }

    public function getColorSchema(): ?string
    {
        return $this->colorSchema;
    }

    public function setColorSchema(?string $colorSchema): self
    {
        $this->colorSchema = $colorSchema;

        return $this;
    }

    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setIsDisabled(?bool $isDisabled): self
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Gallery $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addLike($this);
        }

        return $this;
    }

    public function removeLike(Gallery $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            $like->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(Gallery $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->addView($this);
        }

        return $this;
    }

    public function removeView(Gallery $view): self
    {
        if ($this->views->contains($view)) {
            $this->views->removeElement($view);
            $view->removeView($this);
        }

        return $this;
    }
}
