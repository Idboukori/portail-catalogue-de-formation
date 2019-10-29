<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Formation", inversedBy="quiz", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="quiz", orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DemandeFormation", mappedBy="quiz")
     */
    private $demandeFormations;


    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->demandeFormations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }


    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
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
            $demandeFormation->setQuiz($this);
        }

        return $this;
    }

    public function removeDemandeFormation(DemandeFormation $demandeFormation): self
    {
        if ($this->demandeFormations->contains($demandeFormation)) {
            $this->demandeFormations->removeElement($demandeFormation);
            // set the owning side to null (unless already changed)
            if ($demandeFormation->getQuiz() === $this) {
                $demandeFormation->setQuiz(null);
            }
        }

        return $this;
    }

}
