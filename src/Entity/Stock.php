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
     * @ORM\ManyToOne(targetEntity=Location::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please select a location.")
     */
    private Location $location;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please select a product.")
     */
    private Product $product;

//    /**
//     * @ORM\Column(type="integer")
//     * @Assert\NotNull(message="Please provide a quantity.")
//     * @Assert\GreaterThan(value=0, message="The quantity must be greater than 0.")
//     */
//    private int $quantity;

//    /**
//     * @ORM\ManyToOne(targetEntity="src\Entity\Purchase")
//     * @ORM\JoinColumn (nullable=false)
//     * @Assert\NotNull(message="Please provide a quantity.")
//     */
//    private Purchase $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(value=0, message="The quantity must be greater than or equal to 0.")
     */
    private int $quantity;

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function increaseQuantity(int $quantity): self
    {
        $this->quantity += $quantity;

        return $this;
    }

    public function decreaseQuantity(int $quantity): self
    {
        // Ensure the quantity does not go below zero
        $this->quantity = max(0, $this->quantity - $quantity);

        return $this;
    }

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

//    public function getQuantity(): ?Purchase
//    {
//        return $this->quantity;
//    }
//
//    public function setQuantity(Purchase $quantity): self
//    {
//        $this->quantity = $quantity;
//
//        return $this;
//    }
//
//    public function increaseQuantity(Purchase $quantity): self
//    {
//        $this->quantity += $quantity;
//
//        return $this;
//    }
//
//    public function decreaseQuantity(Purchase $quantity): self
//    {
//        // Ensure the quantity does not go below zero
//        $this->quantity = max(0, $this->quantity - $quantity);
//
//        return $this;
//    }

//    public function __toString()
//    {
//        return $this->getProduct()->getName() . ' (' . $this->getQuantity() . ')';
//    }

//    public function __toString(): string
//    {
//        return $this->getQuantity();
//    }
}
