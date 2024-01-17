<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private Product $product;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Stock")
     */
    private Stock $quantity;

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
}
