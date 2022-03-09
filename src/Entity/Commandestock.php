<?php

namespace App\Entity;

use App\Repository\CommandestockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=CommandestockRepository::class)
 */
class Commandestock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("commandestock")
     */
    private $id;



    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups("commandestock")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Fournisseur::class, inversedBy="commandestocks")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("commandestock")
     */
    private $fournisseur;

    /**
     * @ORM\ManyToMany(targetEntity=Produit::class, inversedBy="commandestocks")
     * @Groups("commandestock")
     */
    private $produit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="quantite is required")
     * @Assert\NotEqualTo(
     *     value = 0, message="quantite can't be NULL")
     * @Groups("commandestock")
     */
    private $quantite;











    public function __construct()
    {
        $this->produit = new ArrayCollection();
    }

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produit->contains($produit)) {
            $this->produit[] = $produit;
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        $this->produit->removeElement($produit);

        return $this;
    }

    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(?string $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
























}
