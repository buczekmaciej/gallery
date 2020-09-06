<?php

namespace App\Entity;

use App\Repository\ReportsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportsRepository::class)
 */
class Reports
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @ORM\JoinColumn(nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Reasons::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Gallery::class, inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getReason(): ?Reasons
    {
        return $this->reason;
    }

    public function setReason(?Reasons $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getItem(): ?Gallery
    {
        return $this->item;
    }

    public function setItem(?Gallery $item): self
    {
        $this->item = $item;

        return $this;
    }
}
