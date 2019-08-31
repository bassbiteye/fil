<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpediteurRepository")
 */
class Expediteur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"lister"})
     *  @Groups({"code"})
     */
    private $nomE;

    /**
     * @ORM\Column(type="string", length=255)
     *   @Groups({"lister"})
     *  @Groups({"code"})
     */
    private $prenomE;

    /**
     * @ORM\Column(type="integer")
     *  @Groups({"lister"})
     *  @Groups({"code"})
     */
    private $telephoneE;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="expediteur")
     */
    private $beneficiaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="expediteur")
     */
    private $transactions;

    public function __construct()
    {
        $this->beneficiaire = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

  

    

    /**
     * @return Collection|Transaction[]
     */
    public function getBeneficiaire(): Collection
    {
        return $this->beneficiaire;
    }

    public function addBeneficiaire(Transaction $beneficiaire): self
    {
        if (!$this->beneficiaire->contains($beneficiaire)) {
            $this->beneficiaire[] = $beneficiaire;
            $beneficiaire->setExpediteur($this);
        }

        return $this;
    }

    public function removeBeneficiaire(Transaction $beneficiaire): self
    {
        if ($this->beneficiaire->contains($beneficiaire)) {
            $this->beneficiaire->removeElement($beneficiaire);
            // set the owning side to null (unless already changed)
            if ($beneficiaire->getExpediteur() === $this) {
                $beneficiaire->setExpediteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setExpediteur($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getExpediteur() === $this) {
                $transaction->setExpediteur(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of nomE
     */ 
    public function getNomE()
    {
        return $this->nomE;
    }

    /**
     * Set the value of nomE
     *
     * @return  self
     */ 
    public function setNomE($nomE)
    {
        $this->nomE = $nomE;

        return $this;
    }

    /**
     * Get the value of prenomE
     */ 
    public function getPrenomE()
    {
        return $this->prenomE;
    }

    /**
     * Set the value of prenomE
     *
     * @return  self
     */ 
    public function setPrenomE($prenomE)
    {
        $this->prenomE = $prenomE;

        return $this;
    }

    /**
     * Get the value of telephoneE
     */ 
    public function getTelephoneE()
    {
        return $this->telephoneE;
    }

    /**
     * Set the value of telephoneE
     *
     * @return  self
     */ 
    public function setTelephoneE($telephoneE)
    {
        $this->telephoneE = $telephoneE;

        return $this;
    }
}
