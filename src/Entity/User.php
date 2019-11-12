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
     * @ORM\ManyToMany(targetEntity="App\Entity\Gallery")
     */
    private $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
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
}
