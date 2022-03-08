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
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(
 *  fields= {"email"},
 *  message= "l'email que vous avez indiqué déja utilisé !"
 * )
 * @UniqueEntity(
 *  fields= {"username"},
 *  message= "usernmae que vous avez indiqué déja utilisé !"
 * )
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("utilisateur")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true ,nullable=true)
     * @Groups("utilisateur")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups("utilisateur")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length (min="8", minMessage="Votre mot de passe doit faire minimum 8 caractéres")
     * @Groups("utilisateur")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password" ,message="tapez meme mot de passe ")
     * @Groups("utilisateur")
     */
    public $confirm_password ;

    /**
     * @ORM\OneToOne(targetEntity=Panier::class, mappedBy="utilisateur", cascade={"persist", "remove"})
     * @Groups("utilisateur")
     */
    private $panier;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="utilisateur")
     * @Groups("utilisateur")
     */
    private $commandes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true , columnDefinition="ENUM('Bloquer','Debloquer')")
     * @Groups("utilisateur")
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     * @Groups("utilisateur")
     */
    private $email;

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

    /**
     * @ORM\Column(type="string", length=50 ,nullable=true)
     * @Groups("utilisateur")
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("utilisateur")
     */
    private $reset_token;

    /**
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="utilisateur")
     */
    private $missions;

    /**
     * @ORM\OneToMany(targetEntity=Reclamation::class, mappedBy="utilisateur")
     */
    private $reclamations;

    /**
     * @ORM\OneToMany(targetEntity=Commantaire::class, mappedBy="utilisateur")
     */
    private $commantaires;



    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->missions = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->commantaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        // unset the owning side of the relation if necessary
        if ($panier === null && $this->panier !== null) {
            $this->panier->setUtilisateur(null);
        }

        // set the owning side of the relation if necessary
        if ($panier !== null && $panier->getUtilisateur() !== $this) {
            $panier->setUtilisateur($this);
        }

        $this->panier = $panier;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function IsVerified(bool $true)
    {
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions[] = $mission;
            $mission->setUtilisateur($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getUtilisateur() === $this) {
                $mission->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations[] = $reclamation;
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commantaire>
     */
    public function getCommantaires(): Collection
    {
        return $this->commantaires;
    }

    public function addCommantaire(Commantaire $commantaire): self
    {
        if (!$this->commantaires->contains($commantaire)) {
            $this->commantaires[] = $commantaire;
            $commantaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommantaire(Commantaire $commantaire): self
    {
        if ($this->commantaires->removeElement($commantaire)) {
            // set the owning side to null (unless already changed)
            if ($commantaire->getUtilisateur() === $this) {
                $commantaire->setUtilisateur(null);
            }
        }

        return $this;
    }


}