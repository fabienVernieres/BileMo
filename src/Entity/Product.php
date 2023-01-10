<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_product",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="products")
 * )
 *
 */
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"products"}) 
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups({"products"}) 
     */
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    /**
     * @Groups({"products"}) 
     */
    private ?string $description = null;

    #[ORM\Column]
    /**
     * @Groups({"products"}) 
     */
    private ?float $price = null;

    #[ORM\Column]
    /**
     * @Groups({"products"}) 
     */
    private ?int $stock = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    /**
     * @Groups({"products"}) 
     */
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @Groups({"products"}) 
     */
    private ?Brand $brand = null;

    #[ORM\Column(type: "bigint")]
    /**
     * @Groups({"products"}) 
     */
    private ?int $ean = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * get product name
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * set product Name
     *
     * @param  mixed $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * get product Description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * set product Description
     *
     * @param  mixed $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * get product Price
     *
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * set product Price
     *
     * @param  mixed $price
     * @return self
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * get product Stock
     *
     * @return int
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * set product Stock
     *
     * @param  mixed $stock
     * @return self
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * get product Creation Date
     *
     * @return DateTimeInterface
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * set product Creation Date
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
     * get product Brand
     *
     * @return Brand
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * set product Brand
     *
     * @param  mixed $brand
     * @return self
     */
    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * get product Ean
     *
     * @return int
     */
    public function getEan(): ?int
    {
        return $this->ean;
    }

    /**
     * set product Ean
     *
     * @param  mixed $ean
     * @return self
     */
    public function setEan(int $ean): self
    {
        $this->ean = $ean;

        return $this;
    }
}