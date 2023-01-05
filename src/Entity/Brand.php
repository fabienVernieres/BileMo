<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_products_brand",
 *          parameters = { "brand" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="products")
 * )
 *
 */
class Brand
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

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setBrand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrand() === $this) {
                $product->setBrand(null);
            }
        }

        return $this;
    }
}