<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formation", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Beneficiaire", inversedBy="sessions")
     */
    private $Beneficiaire;

    public function __construct()
    {
        $this->Beneficiaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection|Beneficiaire[]
     */
    public function getBeneficiaire(): Collection
    {
        return $this->Beneficiaire;
    }

    public function addBeneficiaire(Beneficiaire $beneficiaire): self
    {
        if (!$this->Beneficiaire->contains($beneficiaire)) {
            $this->Beneficiaire[] = $beneficiaire;
        }

        return $this;
    }

    public function removeBeneficiaire(Beneficiaire $beneficiaire): self
    {
        if ($this->Beneficiaire->contains($beneficiaire)) {
            $this->Beneficiaire->removeElement($beneficiaire);
        }

        return $this;
    }
}
