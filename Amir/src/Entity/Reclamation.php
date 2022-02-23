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
     * @ORM\ManyToOne(targetEntity=Livreur::class, inversedBy="reclamations")
     */
    private $livreur;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="reclamations")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieReclamation::class, inversedBy="reclamations")
     * @Assert\NotBlank(message="catégorie n'est pas sélectionée ")
     */
    private $categorieReclamation;

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

    public function getLivreur(): ?livreur
    {
        return $this->livreur;
    }

    public function setLivreur(?livreur $livreur): self
    {
        $this->livreur = $livreur;

        return $this;
    }

    public function getClient(): ?client
    {
        return $this->client;
    }

    public function setClient(?client $client): self
    {
        $this->client = $client;

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
}
