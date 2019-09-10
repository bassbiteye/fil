<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 */
class Operation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     *  @Assert\Positive(
     * message="cette valeur doit être positive"
     * )
     *  @Groups({"liste"})
     */
    private $montantdepose;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     *  @Assert\Positive(
     * message="cette valeur doit être positive"
     * )
     * @Groups({"liste"})
     */
    private $monatantAvantDepot;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"liste"})
     */
    private $DateDepot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="operations")
     * @Groups({"liste"})
     */
    private $compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="operations")
    * @Groups({"liste"})

     */
    private $caissier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantdepose(): ?int
    {
        return $this->montantdepose;
    }

    public function setMontantdepose(int $montantdepose): self
    {
        $this->montantdepose = $montantdepose;

        return $this;
    }

    public function getMonatantAvantDepot(): ?int
    {
        return $this->monatantAvantDepot;
    }

    public function setMonatantAvantDepot(int $monatantAvantDepot): self
    {
        $this->monatantAvantDepot = $monatantAvantDepot;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->DateDepot;
    }

    public function setDateDepot(\DateTimeInterface $DateDepot): self
    {
        $this->DateDepot = $DateDepot;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getCaissier(): ?User
    {
        return $this->caissier;
    }

    public function setCaissier(?User $caissier): self
    {
        $this->caissier = $caissier;

        return $this;
    }
}
