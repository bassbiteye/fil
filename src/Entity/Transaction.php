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
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"code"})
     */
    private $libTransaction;

    /**
     * @ORM\Column(type="bigint")
     *   @Groups({"lister"})
     *  @Groups({"code"})
     */
    private $montantTransaction;

    /**
     * @ORM\Column(type="bigint")
     *   @Groups({"lister"})
     */
    private $codeSecret;

    /**
     * @ORM\Column(type="boolean") 
     * @Groups({"code"})
     */
    private $validate;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     *   @Groups({"lister"})
     */
    private $cni;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *   @Groups({"lister"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="datetime")
     *   @Groups({"lister"})
     *  @Groups({"code"})
     */
    private $dateEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Expediteur", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
       * @Groups({"lister"})
       *  @Groups({"code"})
     */
    private $expediteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Beneficiaire", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     *  @Groups({"lister"})
     * @Groups({"code"})
     */
    private $beneficiaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
   
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tarifs", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tarifs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComProprietaire", mappedBy="transaction")
     */
    private $comProprietaires;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComEtat", mappedBy="transaction")
     */
    private $comEtats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="retrait")
     */
    private $userRetrait;

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

}
