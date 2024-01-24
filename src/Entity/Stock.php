<?php

// src/Entity/Stock.php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
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

    /**
     * @ORM\ManyToOne(targetEntity=Purchase::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Purchase $quantity = null;

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

    public function getQuantity(): ?Purchase
    {
        return $this->quantity;
    }

    public function setQuantity(?Purchase $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function increaseQuantity(int $amount): self
    {
        if ($this->quantity === null) {
            throw new LogicException('Cannot increase quantity on a stock with no associated purchase.');
        }

        $currentQuantity = $this->quantity->getQuantity();

        // Increase the quantity by the specified amount
        $this->quantity->setQuantity($currentQuantity + $amount);

        return $this;
    }
}
