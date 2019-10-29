<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BeneficiaireRepository")
 */
class Beneficiaire
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Formation", inversedBy="beneficiaires")
     */
    private $formations;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\DemandeFormateur", mappedBy="beneficiaire", cascade={"persist", "remove"})
     */
    private $demandeFormateur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DemandeFormation", mappedBy="beneficiaire")
     */
    private $demandeFormations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Session", mappedBy="Beneficiaire")
     */
    private $sessions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="benef")
     */
    private $commentaires;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
        $this->demandeFormations = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function __toString(){
        return $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->contains($formation)) {
            $this->formations->removeElement($formation);
        }

        return $this;
    }

    public function getDemandeFormateur(): ?DemandeFormateur
    {
        return $this->demandeFormateur;
    }

    public function setDemandeFormateur(DemandeFormateur $demandeFormateur): self
    {
        $this->demandeFormateur = $demandeFormateur;

        // set the owning side of the relation if necessary
        if ($this !== $demandeFormateur->getBeneficiaire()) {
            $demandeFormateur->setBeneficiaire($this);
        }

        return $this;
    }

    /**
     * @return Collection|DemandeFormation[]
     */
    public function getDemandeFormations(): Collection
    {
        return $this->demandeFormations;
    }

    public function addDemandeFormation(DemandeFormation $demandeFormation): self
    {
        if (!$this->demandeFormations->contains($demandeFormation)) {
            $this->demandeFormations[] = $demandeFormation;
            $demandeFormation->setBeneficiaire($this);
        }

        return $this;
    }

    public function removeDemandeFormation(DemandeFormation $demandeFormation): self
    {
        if ($this->demandeFormations->contains($demandeFormation)) {
            $this->demandeFormations->removeElement($demandeFormation);
            // set the owning side to null (unless already changed)
            if ($demandeFormation->getBeneficiaire() === $this) {
                $demandeFormation->setBeneficiaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->addBeneficiaire($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeBeneficiaire($this);
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setBenef($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getBenef() === $this) {
                $commentaire->setBenef(null);
            }
        }

        return $this;
    }
}
