<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeFormateurRepository")
 */
class DemandeFormateur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Beneficiaire", inversedBy="demandeFormateur", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $beneficiaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brochureFileName;

    /**
     * @ORM\Column(type="float")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $profession;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeneficiaire(): ?Beneficiaire
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(Beneficiaire $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    public function getBrochureFileName(): ?string
    {
        return $this->brochureFileName;
    }

    public function setBrochureFileName(string $brochureFileName): self
    {
        $this->brochureFileName = $brochureFileName;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

   
}
