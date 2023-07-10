<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $images = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $avertissement = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapitre $id_Chapitre = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $id_cours = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getAvertissement(): ?string
    {
        return $this->avertissement;
    }

    public function setAvertissement(?string $avertissement): self
    {
        $this->avertissement = $avertissement;

        return $this;
    }

    public function getIdChapitre(): ?Chapitre
    {
        return $this->id_Chapitre;
    }

    public function setIdChapitre(?Chapitre $id_Chapitre): self
    {
        $this->id_Chapitre = $id_Chapitre;

        return $this;
    }

    public function getIdCours(): ?Cours
    {
        return $this->id_cours;
    }

    public function setIdCours(?Cours $id_cours): self
    {
        $this->id_cours = $id_cours;

        return $this;
    }




}
