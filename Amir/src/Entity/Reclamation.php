<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="date n'est pas sélectionée ")
     */
    private $date;



    /**
     * @ORM\ManyToOne(targetEntity=CategorieReclamation::class, inversedBy="reclamations")
     * @Assert\NotBlank(message="catégorie n'est pas sélectionée ")
     */
    private $categorieReclamation;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="reclamations")
     */
    private $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }




    public function getCategorieReclamation(): ?categorieReclamation
    {
        return $this->categorieReclamation;
    }

    public function setCategorieReclamation(?categorieReclamation $categorieReclamation): self
    {
        $this->categorieReclamation = $categorieReclamation;

        return $this;
    }

    public function getUtilisateur(): ?utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
