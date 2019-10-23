<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    public function __toString()
    {
        return $this->libTransaction;
    
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"code"})
     * @Groups({"trans","users"})

     */
    private $libTransaction;

    /**
     * @ORM\Column(type="bigint")
     *   @Groups({"trans"})
     *  @Groups({"code"})
     *  @Groups({"trans","users"})

     */
    private $montantTransaction;

    /**
     * @ORM\Column(type="bigint")
     *   @Groups({"trans"})
     */
    private $codeSecret;

    /**
     * @ORM\Column(type="boolean") 
     * @Groups({"code"})
     */
    private $validate;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     *   @Groups({"trans"})
     */
    private $cni;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *   @Groups({"trans"})
     *  @Groups({"trans","users"})

     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="datetime")
     *   @Groups({"trans"})
     *  @Groups({"code"})
     * @Groups({"trans","users"})
     */
    private $dateEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Expediteur", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
       * @Groups({"trans"})
       *  @Groups({"code"})
      * @Groups({"trans","users"})

     */
    private $expediteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Beneficiaire", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     *  @Groups({"trans"})
     * @Groups({"code"})
     * @Groups({"contrat","users"})

     */
    private $beneficiaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"trans"})

     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tarifs", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"trans"})
     *  @Groups({"contrat","users"})

     */
    private $tarifs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="retrait")
     * @Groups({"trans"})
     */
    private $userRetrait;

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"trans"})
     */
    private $comEtat;

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"trans"})
     */
    private $comProprietaire;

    /**
     * @ORM\Column(type="bigint",nullable=true)
     * @Groups({"trans"})
     */
    private $comRetrait;

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"trans"})
     */
    private $comEnvoie;

    public function __construct()
    {
        $this->comProprietaires = new ArrayCollection();
        $this->comEtats = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibTransaction(): ?string
    {
        return $this->libTransaction;
    }

    public function setLibTransaction(string $libTransaction): self
    {
        $this->libTransaction = $libTransaction;

        return $this;
    }

    public function getMontantTransaction(): ?int
    {
        return $this->montantTransaction;
    }

    public function setMontantTransaction(int $montantTransaction): self
    {
        $this->montantTransaction = $montantTransaction;

        return $this;
    }

    public function getCodeSecret(): ?int
    {
        return $this->codeSecret;
    }

    public function setCodeSecret(int $codeSecret): self
    {
        $this->codeSecret = $codeSecret;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    public function getCni(): ?int
    {
        return $this->cni;
    }

    public function setCni(int $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getExpediteur(): ?Expediteur
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Expediteur $expediteur): self
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getBeneficiaire(): ?Beneficiaire
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Beneficiaire $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTarifs(): ?Tarifs
    {
        return $this->tarifs;
    }

    public function setTarifs(?Tarifs $tarifs): self
    {
        $this->tarifs = $tarifs;

        return $this;
    }

    /**
     * @return Collection|ComProprietaire[]
     */
    public function getComProprietaires(): Collection
    {
        return $this->comProprietaires;
    }

    public function addComProprietaire(ComProprietaire $comProprietaire): self
    {
        if (!$this->comProprietaires->contains($comProprietaire)) {
            $this->comProprietaires[] = $comProprietaire;
            $comProprietaire->setTransaction($this);
        }

        return $this;
    }

    public function removeComProprietaire(ComProprietaire $comProprietaire): self
    {
        if ($this->comProprietaires->contains($comProprietaire)) {
            $this->comProprietaires->removeElement($comProprietaire);
            // set the owning side to null (unless already changed)
            if ($comProprietaire->getTransaction() === $this) {
                $comProprietaire->setTransaction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ComEtat[]
     */
    public function getComEtats(): Collection
    {
        return $this->comEtats;
    }

    public function addComEtat(ComEtat $comEtat): self
    {
        if (!$this->comEtats->contains($comEtat)) {
            $this->comEtats[] = $comEtat;
            $comEtat->setTransaction($this);
        }

        return $this;
    }

    public function removeComEtat(ComEtat $comEtat): self
    {
        if ($this->comEtats->contains($comEtat)) {
            $this->comEtats->removeElement($comEtat);
            // set the owning side to null (unless already changed)
            if ($comEtat->getTransaction() === $this) {
                $comEtat->setTransaction(null);
            }
        }

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getComEtat(): ?int
    {
        return $this->comEtat;
    }

    public function setComEtat(int $comEtat): self
    {
        $this->comEtat = $comEtat;

        return $this;
    }

    public function getComProprietaire(): ?int
    {
        return $this->comProprietaire;
    }

    public function setComProprietaire(int $comProprietaire): self
    {
        $this->comProprietaire = $comProprietaire;

        return $this;
    }

    public function getComRetrait(): ?bool
    {
        return $this->comRetrait;
    }

    public function setComRetrait(bool $comRetrait): self
    {
        $this->comRetrait = $comRetrait;

        return $this;
    }

    public function getComEnvoie(): ?int
    {
        return $this->comEnvoie;
    }

    public function setComEnvoie(int $comEnvoie): self
    {
        $this->comEnvoie = $comEnvoie;

        return $this;
    }

}
