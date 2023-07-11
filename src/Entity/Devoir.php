<?php

namespace App\Entity;

use App\Repository\DevoirRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevoirRepository::class)]
class Devoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomDevoir = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDepot = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateSoumission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $images = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_Cours = null;

    #[ORM\ManyToOne(inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignant $id_Enseignant = null;

    #[ORM\OneToMany(mappedBy: 'id_Devoir', targetEntity: DevoirSoummit::class, orphanRemoval: true)]
    private Collection $devoirSoummits;

    public function __construct()
    {
        $this->devoirSoummits = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDevoir(): ?string
    {
        return $this->nomDevoir;
    }

    public function setNomDevoir(string $nomDevoir): self
    {
        $this->nomDevoir = $nomDevoir;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getDateSoumission(): ?\DateTimeInterface
    {
        return $this->dateSoumission;
    }

    public function setDateSoumission(\DateTimeInterface $dateSoumission): self
    {
        $this->dateSoumission = $dateSoumission;

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(?string $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): self
    {
        $this->fichier = $fichier;

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
            $devoirSoummit->setIdDevoir($this);
        }

        return $this;
    }

    public function removeDevoirSoummit(DevoirSoummit $devoirSoummit): self
    {
        if ($this->devoirSoummits->removeElement($devoirSoummit)) {
            // set the owning side to null (unless already changed)
            if ($devoirSoummit->getIdDevoir() === $this) {
                $devoirSoummit->setIdDevoir(null);
            }
        }

        return $this;
    }


}
