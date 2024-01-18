<?php

// src/Entity/Purchase.php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product")
     * @Assert\NotNull(message="Please select a product.")
     */
    private Product $product;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Stock")
     * @Assert\NotNull(message="Please select a stock quantity.")
     */
    private Stock $quantity;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Location")
     * @Assert\NotNull(message="Please select a location.")
     */
    private Location $location;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?Stock
    {
        return $this->quantity;
    }

    public function setQuantity(Stock $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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
}
