<?php

namespace App\Entity;

use App\Repository\DevoirSoummitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevoirSoummitRepository::class)]

class DevoirSoummit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255)]
    private ?string $devoir_Soummit = null;

    #[ORM\Column]
    private ?float $note = null;

    #[ORM\ManyToOne(inversedBy: 'devoirSoummits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $id_Etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'devoirSoummits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devoir $id_Devoir = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDevoirSoummit(): ?string
    {
        return $this->devoir_Soummit;
    }

    public function setDevoirSoummit(string $devoir_Soummit): self
    {
        $this->devoir_Soummit = $devoir_Soummit;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

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

    public function getIdDevoir(): ?Devoir
    {
        return $this->id_Devoir;
    }

    public function setIdDevoir(?Devoir $id_Devoir): self
    {
        $this->id_Devoir = $id_Devoir;

        return $this;
    }




}
