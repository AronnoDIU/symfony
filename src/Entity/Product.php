<?php

// src/Entity/Product.php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"sale:read"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The name cannot be blank.")
     * @Assert\Length(max=255, maxMessage="The name cannot be longer than {{ limit }} characters.")
     * cascade={"persist", "remove"}
     * @JMS\SerializedName("name")
     * @JMS\Groups({"sale:read"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="float", precision=10, scale=2)
     * @Assert\NotNull(message="The price cannot be null.")
     * @Assert\GreaterThan(value=0, message="The price must be greater than 0.")
//     * @JMS\SerializedName("price")
     */
    private ?float $price = 0.0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unit")
     * @Assert\NotNull(message="Please select a unit.")
//     * @JMS\SerializedName("unit")
     */
    private ?Unit $unit;

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

//    /**
//     * @JMS\VirtualProperty
//     * @JMS\SerializedName("priceFormatted")
//     */
//    public function getPriceFormatted(): ?string
//    {
//        return $this->getPrice() !== null ? number_format($this->getPrice(), 2) : null;
//    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit ?? null;
    }

    public function setUnit(Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
