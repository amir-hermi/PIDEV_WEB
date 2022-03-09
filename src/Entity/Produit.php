<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("produit")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("produit")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("produit")
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("produit")
     */
    private $quantite;

    /**
     * @ORM\ManyToMany(targetEntity=Panier::class, inversedBy="produits")
     * @ORM\JoinTable(name="panierproduit")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("produit")
     */
    private $panier;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("produit")
     */
    private $nom;



    /**
     * @ORM\OneToMany(targetEntity=CommandeProduit::class, mappedBy="produit" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("produit")
     */
    private $commandeProduits;

    /**
     * @ORM\Column(type="string", length=255, nullable=true , columnDefinition="ENUM('XL','M','XXL','L','S','XS','XXXL')")
     * @Assert\NotBlank(message="la taille est obligatoire")
     * @Groups("produit")
     */
    private $taille;
    /**
     * @ORM\ManyToOne(targetEntity=Marque::class, inversedBy="produits" )
     */
    private $marque;
    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="produits")
     */
    private $sousCategire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Commandestock::class, mappedBy="produit")
     * @Groups("commandestock")
     */
    private $commandestocks;

    /**
     * @ORM\ManyToMany(targetEntity=Favorie::class, mappedBy="produit")
     */
    private $favories;

    public function __construct()
    {
        $this->panier = new ArrayCollection();
        $this->commandeProduits = new ArrayCollection();
        $this->commandestocks = new ArrayCollection();
        $this->favories = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPanier(): Collection
    {
        return $this->panier;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->panier->contains($panier)) {
            $this->panier[] = $panier;
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        $this->panier->removeElement($panier);

        return $this;
    }



    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

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
            $commandeProduit->setProduit($this);
        }

        return $this;
    }

    public function removeCommandeProduit(CommandeProduit $commandeProduit): self
    {
        if ($this->commandeProduits->removeElement($commandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandeProduit->getProduit() === $this) {
                $commandeProduit->setProduit(null);
            }
        }

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(?string $taille): self
    {
        $this->taille = $taille;

        return $this;
    }
    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getSousCategire(): ?SousCategorie
    {
        return $this->sousCategire;
    }

    public function setSousCategire(?SousCategorie $sousCategire): self
    {
        $this->sousCategire = $sousCategire;

        return $this;
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
    /**
     * @return Collection|Commandestock[]
     */
    public function getCommandestocks(): Collection
    {
        return $this->commandestocks;
    }

    public function addCommandestock(Commandestock $commandestock): self
    {
        if (!$this->commandestocks->contains($commandestock)) {
            $this->commandestocks[] = $commandestock;
            $commandestock->addProduit($this);
        }

        return $this;
    }

    public function removeCommandestock(Commandestock $commandestock): self
    {
        if ($this->commandestocks->removeElement($commandestock)) {
            $commandestock->removeProduit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorie>
     */
    public function getFavories(): Collection
    {
        return $this->favories;
    }

    public function addFavory(Favorie $favory): self
    {
        if (!$this->favories->contains($favory)) {
            $this->favories[] = $favory;
            $favory->addProduit($this);
        }

        return $this;
    }

    public function removeFavory(Favorie $favory): self
    {
        if ($this->favories->removeElement($favory)) {
            $favory->removeProduit($this);
        }

        return $this;
    }


}
