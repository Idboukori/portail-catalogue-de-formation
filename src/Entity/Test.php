<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestRepository")
 */
class Test
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $intitule;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="test", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Qa", mappedBy="test", orphanRemoval=true)
     */
    private $qa;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Chapitre", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $chapitre;


    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->qa = new ArrayCollection();
    }
    public function __toString(){
        return $this->intitule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setTest($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getTest() === $this) {
                $note->setTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Qa[]
     */
    public function getQa(): Collection
    {
        return $this->qa;
    }

    public function addQa(Qa $qa): self
    {
        if (!$this->qa->contains($qa)) {
            $this->qa[] = $qa;
            $qa->setTest($this);
        }

        return $this;
    }

    public function removeQa(Qa $qa): self
    {
        if ($this->qa->contains($qa)) {
            $this->qa->removeElement($qa);
            // set the owning side to null (unless already changed)
            if ($qa->getTest() === $this) {
                $qa->setTest(null);
            }
        }

        return $this;
    }

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(Chapitre $chapitre): self
    {
        $this->chapitre = $chapitre;

        return $this;
    }

   
}
