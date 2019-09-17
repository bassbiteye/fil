<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    public function __toString()
    {
        return $this->nom;
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"contrat","users"})
     * @Groups({"lister"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20,nullable=false)
    
     * @Assert\NotBlank(message =" le username ne doit pas etre vide")
     * @Groups({"lister"})
     * @Groups({"contrat"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     *@Assert\NotBlank(message =" le role ne doit pas etre vide")
     * @Groups({"lister"})
     *@Groups({"contrat","users"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message =" le mot de passe ne doit pas etre vide")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10 , nullable=false)
     * @Assert\NotBlank(message =" le nom ne doit pas etre vide")
     * @Groups({"lister"}),
     *@Groups({"contrat","users"})
     * @Groups({"trans"})

     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message =" le prenom ne doit pas etre vide")
     * @Groups({"lister"})
     *@Groups({"contrat","users"})
     * @Groups({"trans"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message =" le numero ne doit pas etre vide")
     *  @Assert\Length(
     *      min = 9,
     *      max = 14,
     *      minMessage = "le numero doit etre au moins {{ limit }} chiffres",
     *      maxMessage = "le numero doit etre au max {{ limit }} chiffres",
     * )
     * @Assert\NotBlank(message="Vous devez insérer un téléphone")
     * @Assert\Regex(
     *     pattern="/^(\+[1-9][0-9]*(\([0-9]*\)|-[0-9]*-))?[0]?[1-9][0-9\-]*$/",
     *     match=true,
     *     message="Votre numero ne doit pas contenir de lettre"
     * )
     *  @Assert\Positive(
     * message="cette valeur doit être positive"
     * )
     * @Groups({"lister"})
     *  @Assert\NotBlank
     *  @Groups({"users"})
     * @Groups({"liste"})
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partenaire", mappedBy="createdBy")
     * 
     */
    private $partenaires;
    /**
   
     *@Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {
     *         "image/jpeg",
     *         "image/pjpeg",
     *         "image/png",
     *     },
     *   
     * mimeTypesMessage = "Veuillez saisir un bon format d\'image"
     *  ) 
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="User", fileNameProperty="imageName")
    
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime" ,nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="users")
     *  @Groups({"contrat"})
     * @Groups({"affect"})
     *@Groups({"contrat","users"}) 
     * @Groups({"lister"})
     */
    private $partenaire;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"lister"})
     *@Groups({"contrat","users"})
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="users")
     *@Groups({"contrat","users"})
     */
    private $Compte;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operation", mappedBy="caissier")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="user")
     * @Groups({"lister"})
     * @Groups({"contrat","users"})
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="userRetrait")
     */
    private $retrait;

    public function __construct()
    {
        $this->partenaires = new ArrayCollection();
        $this->operations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->retrait = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Partenaire[]
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(Partenaire $partenaire): self
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires[] = $partenaire;
            $partenaire->setCreatedBy($this);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): self
    {
        if ($this->partenaires->contains($partenaire)) {
            $this->partenaires->removeElement($partenaire);
            // set the owning side to null (unless already changed)
            if ($partenaire->getCreatedBy() === $this) {
                $partenaire->setCreatedBy(null);
            }
        }

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->Compte;
    }

    public function setCompte(?Compte $Compte): self
    {
        $this->Compte = $Compte;

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
            $operation->setCaissier($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->contains($operation)) {
            $this->operations->removeElement($operation);
            // set the owning side to null (unless already changed)
            if ($operation->getCaissier() === $this) {
                $operation->setCaissier(null);
            }
        }

        return $this;
    }
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
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
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getRetrait(): Collection
    {
        return $this->retrait;
    }

    public function addRetrait(Transaction $retrait): self
    {
        if (!$this->retrait->contains($retrait)) {
            $this->retrait[] = $retrait;
            $retrait->setUserRetrait($this);
        }

        return $this;
    }

    public function removeRetrait(Transaction $retrait): self
    {
        if ($this->retrait->contains($retrait)) {
            $this->retrait->removeElement($retrait);
            // set the owning side to null (unless already changed)
            if ($retrait->getUserRetrait() === $this) {
                $retrait->setUserRetrait(null);
            }
        }

        return $this;
    }
}
