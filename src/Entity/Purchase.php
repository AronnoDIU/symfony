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
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please select a product.")
     */
    private Product $product;

//    /**
//     * @ORM\ManyToOne(targetEntity="App\Entity\Stock")
//     * @ORM\JoinColumn(nullable=false)
//     * @Assert\NotNull(message="Please select a stock quantity.")
//     */
//    private Stock $quantity;

//    /**
//     * @ORM\Column(type="integer")
//     * @Assert\NotNull(message="Please provide a quantity.")
//     * @Assert\GreaterThan(value=0, message="The quantity must be greater than 0.")
//     */
//    private int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please select a location.")
     */
    private Location $location;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices={"Draft", "Approve"})
     */
    private string $status;

    public function __construct()
    {
        // Ensure the status property is initialized
        $this->status = 'Draft';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

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

//    public function getQuantity(): ?Stock
//    {
//        return $this->quantity;
//    }
//
//    public function setQuantity(Stock $quantity): self
//    {
//        $this->quantity = $quantity;
//
//        return $this;
//    }

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="Please provide a quantity.")
     * @Assert\GreaterThan(value=0, message="The quantity must be greater than 0.")
     */
    private int $quantity;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
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

    public function __toString(): string
    {
        return $this->getQuantity();
    }
}
