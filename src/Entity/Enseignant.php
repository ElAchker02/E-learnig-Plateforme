<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
// #[ORM\HasLifecycleCallbacks()]
class Enseignant
{
    // use Timestamps;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

  

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\ManyToOne(inversedBy: 'enseignants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $id_personne = null;

    #[ORM\OneToMany(mappedBy: 'id_Enseignant', targetEntity: Devoir::class, orphanRemoval: true)]
    private Collection $devoirs;

    #[ORM\OneToMany(mappedBy: 'id_Enseignant', targetEntity: Cours::class, orphanRemoval: true)]
    private Collection $cours;

    public function __construct()
    {
        $this->devoirs = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): self
    {
        $this->specialite = $specialite;

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
            $devoir->setIdEnseignant($this);
        }

        return $this;
    }

    public function removeDevoir(Devoir $devoir): self
    {
        if ($this->devoirs->removeElement($devoir)) {
            // set the owning side to null (unless already changed)
            if ($devoir->getIdEnseignant() === $this) {
                $devoir->setIdEnseignant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setIdEnseignant($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdEnseignant() === $this) {
                $cour->setIdEnseignant(null);
            }
        }

        return $this;
    }
}
