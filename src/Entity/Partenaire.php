<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartenaireRepository")
 *  @UniqueEntity("raisonSocial" ,message =" Cette raison Sociale  existe déja") 
 * @UniqueEntity(
 *     fields={"ninea","raisonSocial"},
 *     errorPath="ninea",
 *     message="Ce ninea  existe déja"
 * )
 */
class Partenaire
{
    public function __toString()
    {
        return $this->raisonSocial;
    
    }

    // ...

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *  @Assert\NotBlank(
     * message =" la raison social ne doit pas etre vide")
     *  @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "la raison Social dois être au moins {{ limit }} carateres",
     *      maxMessage = "la raison Social ne peut pas être plus grand que {{ limit }} carateres"
     * )
     *@Groups({"lister"})
     *@Groups({"contrat"})
     */
    private $raisonSocial;

    /**
     * @ORM\Column(type="string", length=25, nullable=false)
     *  @Assert\NotBlank
     * @Assert\Length(
     *      min = 5,
     *      max = 20,
     *      minMessage = " adresse dois être au moins {{ limit }} carateres",
     *      maxMessage = "adresse ne peut pas être plus grand que {{ limit }} carateres"
     * )
     * @Groups({"lister"})
     *@Groups({"contrat"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="bigint")
     *  @Assert\NotBlank
     *   @Assert\Length(
     *     min = 12,
     *      minMessage  = " NINEA dois être 12 numeros"
     * )
     * @Assert\Positive(
     * message="cette valeur doit être positive"
     * )
     * @Groups({"lister"})
     *@Groups({"contrat"})
     */
    private $ninea;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partenaires")
     * @ORM\JoinColumn(nullable=false)

     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="partenaire")
      *@Groups({"contrat"})
      *  @Groups({"lister"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="partenaire")
    * @Groups({"lister"})
     */
    private $comptes;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $etat;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSocial(): ?string
    {
        return $this->raisonSocial;
    }

    public function setRaisonSocial(?string $raisonSocial): self
    {
        $this->raisonSocial = $raisonSocial;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNinea(): ?int
    {
        return $this->ninea;
    }

    public function setNinea(int $ninea): self
    {
        $this->ninea = $ninea;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

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
            $user->setPartenaire($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getPartenaire() === $this) {
                $user->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setPartenaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getPartenaire() === $this) {
                $compte->setPartenaire(null);
            }
        }

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
}
