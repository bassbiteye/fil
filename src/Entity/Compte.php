<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 *  @UniqueEntity("numCompte" ,message =" Ce numero Compte  existe déja") 
 */
class Compte
{
    public function __toString()
    {
        return $this->numCompte;
    }
    // ...
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string" , length=20)
     * @Assert\NotBlank
     * @Groups({"liste"})
     *   @Groups({"lister"})
     *@Groups({"compte"})
     *@Groups({"affect"})
      *@Groups({"contrat","users"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="bigint")
     *  @Assert\NotBlank
     * @Groups({"liste"})
     * @Groups({"lister"})
     *@Groups({"compte"})
     * @Groups({"contrat","users"})
     */
    private $solde;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="comptes")
     * @Groups({"compte"})
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operation", mappedBy="compte")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="Compte")
     
     */
    private $users;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setCompte($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->contains($operation)) {
            $this->operations->removeElement($operation);
            // set the owning side to null (unless already changed)
            if ($operation->getCompte() === $this) {
                $operation->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompte($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCompte() === $this) {
                $user->setCompte(null);
            }
        }

        return $this;
    }
}
