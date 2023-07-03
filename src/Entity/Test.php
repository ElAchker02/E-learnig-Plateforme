<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
// #[ORM\HasLifecycleCallbacks()]
class Test
{
    // use Timestamps;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomTest = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\ManyToOne(inversedBy: 'tests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_Cours = null;

    #[ORM\OneToMany(mappedBy: 'id_Test', targetEntity: NoteTest::class, orphanRemoval: true)]
    private Collection $noteTests;

    #[ORM\OneToMany(mappedBy: 'id_Test', targetEntity: Question::class, orphanRemoval: true)]
    private Collection $questions;

    public function __construct()
    {
        $this->noteTests = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTest(): ?string
    {
        return $this->nomTest;
    }

    public function setNomTest(string $nomTest): self
    {
        $this->nomTest = $nomTest;

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

    public function getIdCours(): ?Cours
    {
        return $this->id_Cours;
    }

    public function setIdCours(?Cours $id_Cours): self
    {
        $this->id_Cours = $id_Cours;

        return $this;
    }

    /**
     * @return Collection<int, NoteTest>
     */
    public function getNoteTests(): Collection
    {
        return $this->noteTests;
    }

    public function addNoteTest(NoteTest $noteTest): self
    {
        if (!$this->noteTests->contains($noteTest)) {
            $this->noteTests->add($noteTest);
            $noteTest->setIdTest($this);
        }

        return $this;
    }

    public function removeNoteTest(NoteTest $noteTest): self
    {
        if ($this->noteTests->removeElement($noteTest)) {
            // set the owning side to null (unless already changed)
            if ($noteTest->getIdTest() === $this) {
                $noteTest->setIdTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setIdTest($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getIdTest() === $this) {
                $question->setIdTest(null);
            }
        }

        return $this;
    }
}
