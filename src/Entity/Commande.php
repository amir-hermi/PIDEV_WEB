<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"commande"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"commande"})
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=255 , columnDefinition="ENUM('Confirmée', 'Annulée','En attente','En cours de preparation','Livraison en cours','livrée')")
     * @Groups({"commande"})
     */
    private $status;



    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"commande"})
     */
    private $montant;





    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"commande"})
     */
    private $reference;



    /**
     * @ORM\OneToMany(targetEntity=CommandeProduit::class, mappedBy="commande" , cascade={"persist", "remove"})
     * @Groups({"commande"})
     */
    private $commandeProduits;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="commandes")
     * @Groups({"commande","utilisateur"})
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="commandes" )
     */
    private $mission;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notifClient;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notifAdmin;

    public function __construct()
    {


        $this->date_creation = date_create();
        $this->commandeProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status= $status;

        return $this;
    }





    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }












    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }



    /**
     * @return Collection|CommandeProduit[]
     */
    public function getCommandeProduits(): Collection
    {
        return $this->commandeProduits;
    }

    public function addCommandeProduit(CommandeProduit $commandeProduit): self
    {
        if (!$this->commandeProduits->contains($commandeProduit)) {
            $this->commandeProduits[] = $commandeProduit;
            $commandeProduit->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeProduit(CommandeProduit $commandeProduit): self
    {
        if ($this->commandeProduits->removeElement($commandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandeProduit->getCommande() === $this) {
                $commandeProduit->setCommande(null);
            }
        }

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

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }

    public function getNotifClient(): ?bool
    {
        return $this->notifClient;
    }

    public function setNotifClient(?bool $notifClient): self
    {
        $this->notifClient = $notifClient;

        return $this;
    }

    public function getNotifAdmin(): ?bool
    {
        return $this->notifAdmin;
    }

    public function setNotifAdmin(?bool $notifAdmin): self
    {
        $this->notifAdmin = $notifAdmin;

        return $this;
    }
}
