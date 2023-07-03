<?php

namespace App\Entity;

use App\Repository\EtudiantCoursPayantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantCoursPayantRepository::class)]
class EtudiantCoursPayant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantCoursPayants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $id_Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantCoursPayants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_Cours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;

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
