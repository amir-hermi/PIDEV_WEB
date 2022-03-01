<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("panier")
     */
    private $id;



    /**
     * @ORM\ManyToMany(targetEntity=Produit::class, mappedBy="panier")
     * @ORM\JoinTable(name="panierproduit")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("panier")
     */
    private $produits;

    /**
     * @ORM\OneToOne(targetEntity=Utilisateur::class, inversedBy="panier", cascade={"persist", "remove"})
     * @Groups("panier")
     */
    private $utilisateur;





    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->addPanier($this);
        }

        return $this;
    }


    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removePanier($this);
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


}
