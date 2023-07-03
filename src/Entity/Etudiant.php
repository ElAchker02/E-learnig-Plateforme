<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
// #[ORM\HasLifecycleCallbacks()]
class Etudiant
{
    // use Timestamps;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filiere = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $id_personne = null;

    #[ORM\OneToMany(mappedBy: 'id_Etudiant', targetEntity: EtudiantCoursPayant::class, orphanRemoval: true)]
    private Collection $etudiantCoursPayants;

    #[ORM\OneToMany(mappedBy: 'id_Etudiant', targetEntity: Progression::class, orphanRemoval: true)]
    private Collection $progressions;

    #[ORM\OneToMany(mappedBy: 'id_Etudiant', targetEntity: DevoirSoummit::class, orphanRemoval: true)]
    private Collection $devoirSoummits;

    #[ORM\OneToMany(mappedBy: 'id_Etudiant', targetEntity: NoteTest::class, orphanRemoval: true)]
    private Collection $noteTests;

    public function __construct()
    {
        $this->etudiantCoursPayants = new ArrayCollection();
        $this->progressions = new ArrayCollection();
        $this->devoirSoummits = new ArrayCollection();
        $this->noteTests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(string $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getIdPersonne(): ?Personne
    {
        return $this->id_personne;
    }

    public function setIdPersonne(?Personne $id_personne): self
    {
        $this->id_personne = $id_personne;

        return $this;
    }

    /**
     * @return Collection<int, EtudiantCoursPayant>
     */
    public function getEtudiantCoursPayants(): Collection
    {
        return $this->etudiantCoursPayants;
    }

    public function addEtudiantCoursPayant(EtudiantCoursPayant $etudiantCoursPayant): self
    {
        if (!$this->etudiantCoursPayants->contains($etudiantCoursPayant)) {
            $this->etudiantCoursPayants->add($etudiantCoursPayant);
            $etudiantCoursPayant->setIdEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantCoursPayant(EtudiantCoursPayant $etudiantCoursPayant): self
    {
        if ($this->etudiantCoursPayants->removeElement($etudiantCoursPayant)) {
            // set the owning side to null (unless already changed)
            if ($etudiantCoursPayant->getIdEtudiant() === $this) {
                $etudiantCoursPayant->setIdEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Progression>
     */
    public function getProgressions(): Collection
    {
        return $this->progressions;
    }

    public function addProgression(Progression $progression): self
    {
        if (!$this->progressions->contains($progression)) {
            $this->progressions->add($progression);
            $progression->setIdEtudiant($this);
        }

        return $this;
    }

    public function removeProgression(Progression $progression): self
    {
        if ($this->progressions->removeElement($progression)) {
            // set the owning side to null (unless already changed)
            if ($progression->getIdEtudiant() === $this) {
                $progression->setIdEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DevoirSoummit>
     */
    public function getDevoirSoummits(): Collection
    {
        return $this->devoirSoummits;
    }

    public function addDevoirSoummit(DevoirSoummit $devoirSoummit): self
    {
        if (!$this->devoirSoummits->contains($devoirSoummit)) {
            $this->devoirSoummits->add($devoirSoummit);
            $devoirSoummit->setIdEtudiant($this);
        }

        return $this;
    }

    public function removeDevoirSoummit(DevoirSoummit $devoirSoummit): self
    {
        if ($this->devoirSoummits->removeElement($devoirSoummit)) {
            // set the owning side to null (unless already changed)
            if ($devoirSoummit->getIdEtudiant() === $this) {
                $devoirSoummit->setIdEtudiant(null);
            }
        }

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
            $noteTest->setIdEtudiant($this);
        }

        return $this;
    }

    public function removeNoteTest(NoteTest $noteTest): self
    {
        if ($this->noteTests->removeElement($noteTest)) {
            // set the owning side to null (unless already changed)
            if ($noteTest->getIdEtudiant() === $this) {
                $noteTest->setIdEtudiant(null);
            }
        }

        return $this;
    }
}
