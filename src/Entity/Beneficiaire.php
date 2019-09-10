<?php

namespace App\Entity;

use App\Entity\Transaction;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BeneficiaireRepository")
 */
class Beneficiaire
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
     * @Groups({"trans"})

     */
    private $nomb;

    /**
     * @ORM\Column(type="string", length=255)
     *   @Groups({"lister"})
     *  @Groups({"code"})
     * @Groups({"trans"})

     */
    private $prenomb;

    /**
     * @ORM\Column(type="integer")
     *    @Groups({"lister"})
     *  @Groups({"code"})
     * @Groups({"trans"})

     */
    private $telephoneb;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="beneficiaire")
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $transaction->setBeneficiaire($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getBeneficiaire() === $this) {
                $transaction->setBeneficiaire(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of nomb
     */
    public function getNomb()
    {
        return $this->nomb;
    }

    /**
     * Set the value of nomb
     *
     * @return  self
     */
    public function setNomb($nomb)
    {
        $this->nomb = $nomb;

        return $this;
    }

    /**
     * Get the value of prenomb
     */
    public function getPrenomb()
    {
        return $this->prenomb;
    }

    /**
     * Set the value of prenomb
     *
     * @return  self
     */
    public function setPrenomb($prenomb)
    {
        $this->prenomb = $prenomb;

        return $this;
    }

    /**
     * Get the value of telephoneb
     */
    public function getTelephoneb()
    {
        return $this->telephoneb;
    }

    /**
     * Set the value of telephoneb
     *
     * @return  self
     */
    public function setTelephoneb($telephoneb)
    {
        $this->telephoneb = $telephoneb;

        return $this;
    }
}
