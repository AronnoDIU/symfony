<?php

// src/Entity/Sale/Product.php

namespace App\Entity\Sale;

use App\Entity\Sale;
use App\Repository\Sale\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Entity\Product as Original;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="sale_product")
 * @OA\Schema(
 *      title="SaleProduct",
 *      description="Sale\Product entity"
 *  )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the sale\product.")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="float", precision=10, scale=2)
     * @Assert\NotNull(message="The price cannot be null.")
     * @Assert\GreaterThan(value=0, message="The price must be greater than 0.")
     * @JMS\SerializedName("price")
     * @JMS\Groups({"sale:read"})
     * @OA\Property(property="price", type="number", description="The price of the sale\product.")
     */
    private ?float $price;

    /**
     * @@ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please enter a quantity.")
     * @JMS\SerializedName("quantity")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="quantity", type="integer", description="The quantity of the product sold.")
     */
    private ?int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Sale $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     */
    private ?Original $original;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(Sale $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("original")
     * @JMS\Groups({"sale:read"})
     * @OA\Property(description="The original associated with the sale\product.",)
     * @OA\Property(ref=@Model(type=Original::class))
     */
    public function getOriginal(): ?Original
    {
        return $this->original;
    }

    public function setOriginal(Original $original): self
    {
        $this->original = $original;

        return $this;
    }
}
