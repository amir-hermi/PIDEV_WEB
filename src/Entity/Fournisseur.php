<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=FournisseurRepository::class)
 */
class Fournisseur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("fournisseur")
     */
    private $id;




    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\NotBlank(message="adresse_fournisseur is required")
     * @Groups("fournisseur")
     */
    private $adresse_fournisseur;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\NotBlank(message="MDP_fournisseur is required")
     * @Groups("fournisseur")
     */
    private $MDP_fournisseur;

    /**
     * @ORM\OneToMany(targetEntity=Commandestock::class, mappedBy="fournisseur" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("commandestock")
     */
    private $commandestocks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="nom_fournisseur is required")
     * @Assert\Length(
     *      min = 2,
     *      max = 40,
     *      minMessage = "nom_fournisseur must be at least {{ 2 }} characters long",
     *      maxMessage = "nom_fournisseur cannot be longer than {{ 40 }} characters")
     * @Groups("fournisseur")
     */
    private $nom_fournisseur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Nom is required")
     * @Groups("utilisateur")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="telephone is required")
     * @Groups("utilisateur")
     */
    private $tel;



    public function __construct()
    {
        $this->commandestocks = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFournisseur(): ?string
    {
        return $this->id_fournisseur;
    }

    public function setIdFournisseur(string $id_fournisseur): self
    {
        $this->id_fournisseur = $id_fournisseur;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(?int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getAdresseFournisseur(): ?string
    {
        return $this->adresse_fournisseur;
    }

    public function setAdresseFournisseur(string $adresse_fournisseur): self
    {
        $this->adresse_fournisseur = $adresse_fournisseur;

        return $this;
    }

    public function getMDPFournisseur(): ?string
    {
        return $this->MDP_fournisseur;
    }

    public function setMDPFournisseur(string $MDP_fournisseur): self
    {
        $this->MDP_fournisseur = $MDP_fournisseur;

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
            $commandestock->setFournisseur($this);
        }

        return $this;
    }

    public function removeCommandestock(Commandestock $commandestock): self
    {
        if ($this->commandestocks->removeElement($commandestock)) {
            // set the owning side to null (unless already changed)
            if ($commandestock->getFournisseur() === $this) {
                $commandestock->setFournisseur(null);
            }
        }

        return $this;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->nom_fournisseur;
    }

    public function setNomFournisseur(?string $nom_fournisseur): self
    {
        $this->nom_fournisseur = $nom_fournisseur;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image)
    {
        $this->image = $image;

        return $this;
    }










}
