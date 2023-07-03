<?php

namespace App\Entity;

use App\Repository\NoteTestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteTestRepository::class)]
class NoteTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $note = null;

    #[ORM\ManyToOne(inversedBy: 'noteTests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Test $id_Test = null;

    #[ORM\ManyToOne(inversedBy: 'noteTests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $id_Etudiant = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdTest(): ?Test
    {
        return $this->id_Test;
    }

    public function setIdTest(?Test $id_Test): self
    {
        $this->id_Test = $id_Test;

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
}
