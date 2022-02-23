<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $NomAadmin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AdresseAadmin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $TelAdmin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $MDPAdmin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAadmin(): ?string
    {
        return $this->NomAadmin;
    }

    public function setNomAadmin(string $NomAadmin): self
    {
        $this->NomAadmin = $NomAadmin;

        return $this;
    }

    public function getAdresseAadmin(): ?string
    {
        return $this->AdresseAadmin;
    }

    public function setAdresseAadmin(?string $AdresseAadmin): self
    {
        $this->AdresseAadmin = $AdresseAadmin;

        return $this;
    }

    public function getTelAdmin(): ?string
    {
        return $this->TelAdmin;
    }

    public function setTelAdmin(?string $TelAdmin): self
    {
        $this->TelAdmin = $TelAdmin;

        return $this;
    }

    public function getMDPAdmin(): ?string
    {
        return $this->MDPAdmin;
    }

    public function setMDPAdmin(?string $MDPAdmin): self
    {
        $this->MDPAdmin = $MDPAdmin;

        return $this;
    }
}
