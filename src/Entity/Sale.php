<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SaleRepository::class)
 */
class Sale
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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Please select a location.")
     */
    private Location $location;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please enter a quantity.")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices={"Draft", "Approve", "Pending", "Complete"})
     */
    private string $status;

    public function __construct()
    {
        // Ensure the status property is initialized
        $this->status = 'Draft';
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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
}
