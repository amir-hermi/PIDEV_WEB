<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CommandeProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ORM\Entity(repositoryClass=CommandeProduitRepository::class)
 */
class CommandeProduit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("commandeProduit")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("commandeProduit")
     */
    private $quantiteProduit;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="commandeProduits" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("commandeProduit")
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="commandeProduits" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("commandeProduit")
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteProduit(): ?int
    {
        return $this->quantiteProduit;
    }

    public function setQuantiteProduit(?int $quantiteProduit): self
    {
        $this->quantiteProduit = $quantiteProduit;

        return $this;
    }

    public function getProduit(): ?produit
    {
        return $this->produit;
    }

    public function setProduit(?produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getCommande(): ?commande
    {
        return $this->commande;
    }

    public function setCommande(?commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
