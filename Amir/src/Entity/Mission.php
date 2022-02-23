<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MissionRepository::class)
 */
class Mission
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
     * @ORM\ManyToOne(targetEntity=Livreur::class, inversedBy="missions")
     * @Assert\NotBlank(message="livreur n'est pas sélectionée ")
     */
    private $livreur;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="missions")
     */
    private $admin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="adresse est vide")
     */
    private $adresse;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdmin(): ?admin
    {
        return $this->admin;
    }

    public function setAdmin(?admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
}
