<?php

namespace App\Entity;

use App\Repository\ProgressionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgressionRepository::class)]
class Progression
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column]
    private ?int $chap_Courant = null;

    #[ORM\Column]
    private ?bool $done = null;

    #[ORM\ManyToOne(inversedBy: 'progressions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $id_Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'progressions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_Cours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapCourant(): ?int
    {
        return $this->chap_Courant;
    }

    public function setChapCourant(int $chap_Courant): self
    {
        $this->chap_Courant = $chap_Courant;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getIdEtudiant(): ?Etudiant
    {
        return $this->id_Etudiant;
    }

    public function setIdEtudiant(?Etudiant $id_Etudiant): self
    {
        $this->id_Etudiant = $id_Etudiant;

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
}
