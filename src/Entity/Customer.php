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

    /**
     * get customer Id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get customer Lastname
     *
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * set customer Lastname
     *
     * @param  mixed $lastname
     * @return self
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * get customer Firstname
     *
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * set customer Firstname
     *
     * @param  mixed $firstname
     * @return self
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * get customer Email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * set customer Email
     *
     * @param  mixed $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * get customer Creation Date
     *
     * @return DateTimeInterface
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * set customer Creation Date
     *
     * @param  mixed $creationDate
     * @return self
     */
    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * get user from client
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * set user from client
     *
     * @param  mixed $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}