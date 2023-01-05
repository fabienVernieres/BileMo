<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_customer",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="customers")
 * )
 *
 */
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"customers"}) 
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"customers"}) 
     */
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    /**
     * @Groups({"customers"}) 
     */
    private ?string $firstname = null;

    #[ORM\Column(length: 255, unique: true)]
    /**
     * @Groups({"customers"}) 
     */
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    /**
     * @Groups({"customers"}) 
     */
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

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
}