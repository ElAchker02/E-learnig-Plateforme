<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
// #[ORM\HasLifecycleCallbacks()]
class Cours
{
    // use Timestamps;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCours = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column]
    private ?int $nbChapitres = null;

    #[ORM\Column]
    private ?bool $estPayant = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $prix = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $id_Categorie = null;

    #[ORM\OneToMany(mappedBy: 'id_Cours', targetEntity: Chapitre::class, orphanRemoval: true)]
    private Collection $chapitres;

    #[ORM\OneToMany(mappedBy: 'id_Cours', targetEntity: EtudiantCoursPayant::class, orphanRemoval: true)]
    private Collection $etudiantCoursPayants;

    #[ORM\OneToMany(mappedBy: 'id_Cours', targetEntity: Progression::class, orphanRemoval: true)]
    private Collection $progressions;

    #[ORM\OneToMany(mappedBy: 'id_Cours', targetEntity: Devoir::class, orphanRemoval: true)]
    private Collection $devoirs;

    #[ORM\OneToMany(mappedBy: 'id_Cours', targetEntity: Test::class, orphanRemoval: true)]
    private Collection $tests;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignant $id_Enseignant = null;

    #[ORM\OneToMany(mappedBy: 'id_cours', targetEntity: Partie::class, orphanRemoval: true)]
    private Collection $parties;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;




    public function __construct()
    {
        $this->chapitres = new ArrayCollection();
        $this->etudiantCoursPayants = new ArrayCollection();
        $this->progressions = new ArrayCollection();
        $this->devoirs = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->parties = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCours(): ?string
    {
        return $this->nomCours;
    }

    public function setNomCours(string $nomCours): self
    {
        $this->nomCours = $nomCours;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getNbChapitres(): ?int
    {
        return $this->nbChapitres;
    }

    public function setNbChapitres(int $nbChapitres): self
    {
        $this->nbChapitres = $nbChapitres;

        return $this;
    }

    public function isEstPayant(): ?bool
    {
        return $this->estPayant;
    }

    public function setEstPayant(bool $estPayant): self
    {
        $this->estPayant = $estPayant;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->id_Categorie;
    }

    public function setIdCategorie(?Categorie $id_Categorie): self
    {
        $this->id_Categorie = $id_Categorie;

        return $this;
    }

    /**
     * @return Collection<int, Chapitre>
     */
    public function getChapitres(): Collection
    {
        return $this->chapitres;
    }

    public function addChapitre(Chapitre $chapitre): self
    {
        if (!$this->chapitres->contains($chapitre)) {
            $this->chapitres->add($chapitre);
            $chapitre->setIdCours($this);
        }

        return $this;
    }

    public function removeChapitre(Chapitre $chapitre): self
    {
        if ($this->chapitres->removeElement($chapitre)) {
            // set the owning side to null (unless already changed)
            if ($chapitre->getIdCours() === $this) {
                $chapitre->setIdCours(null);
            }
        }

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
            $etudiantCoursPayant->setIdCours($this);
        }

        return $this;
    }

    public function removeEtudiantCoursPayant(EtudiantCoursPayant $etudiantCoursPayant): self
    {
        if ($this->etudiantCoursPayants->removeElement($etudiantCoursPayant)) {
            // set the owning side to null (unless already changed)
            if ($etudiantCoursPayant->getIdCours() === $this) {
                $etudiantCoursPayant->setIdCours(null);
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
            $progression->setIdCours($this);
        }

        return $this;
    }

    public function removeProgression(Progression $progression): self
    {
        if ($this->progressions->removeElement($progression)) {
            // set the owning side to null (unless already changed)
            if ($progression->getIdCours() === $this) {
                $progression->setIdCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Devoir>
     */
    public function getDevoirs(): Collection
    {
        return $this->devoirs;
    }

    public function addDevoir(Devoir $devoir): self
    {
        if (!$this->devoirs->contains($devoir)) {
            $this->devoirs->add($devoir);
            $devoir->setIdCours($this);
        }

        return $this;
    }

    public function removeDevoir(Devoir $devoir): self
    {
        if ($this->devoirs->removeElement($devoir)) {
            // set the owning side to null (unless already changed)
            if ($devoir->getIdCours() === $this) {
                $devoir->setIdCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Test>
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests->add($test);
            $test->setIdCours($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->removeElement($test)) {
            // set the owning side to null (unless already changed)
            if ($test->getIdCours() === $this) {
                $test->setIdCours(null);
            }
        }

        return $this;
    }

    public function getIdEnseignant(): ?Enseignant
    {
        return $this->id_Enseignant;
    }

    public function setIdEnseignant(?Enseignant $id_Enseignant): self
    {
        $this->id_Enseignant = $id_Enseignant;

        return $this;
    }

    /**
     * @return Collection<int, Partie>
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Partie $party): self
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
            $party->setIdCours($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): self
    {
        if ($this->parties->removeElement($party)) {
            // set the owning side to null (unless already changed)
            if ($party->getIdCours() === $this) {
                $party->setIdCours(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }





}
