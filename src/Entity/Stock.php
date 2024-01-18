<?php

// src/Entity/Stock.php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToMany(targetEntity=Location::class)
     * @Assert\NotNull(message="Please select a location.")
     */
    private Location $location;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     * @Assert\NotNull(message="Please select a product.")
     */
    private Product $product;

    /**
     * @ORM\Column(type="float")
     * @ORM\ManyToMany(targetEntity=Purchase::class)
     * @Assert\NotNull(message="Please provide a quantity.")
     * @Assert\GreaterThan(value=0, message="The quantity must be greater than 0.")
     */
    private float $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
