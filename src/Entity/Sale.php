<?php

// src/Entity/Sale.php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Sale\Product as SaleProduct;

/**
 * @ORM\Entity(repositoryClass=SaleRepository::class)
 * @OA\Schema(
 *     title="Sale",
 *     description="Sale entity"
 * )
 */
class Sale
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the sale.")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Groups({"sale:read", "sale:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(property="customer", ref=@Model(type=Customer::class), description="The customer associated with the sale.")
     */
    private Customer $customer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\Product", mappedBy="sale", cascade={"persist"})
     * @ORM\JoinColumn(name="sale_product")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="products", ref=@Model(type=SaleProduct::class), description="The products associated with the sale.")
     */
    private Collection $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("App\Entity\Location")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(property="location", ref=@Model(type=Location::class), description="The location associated with the sale.")
     */
    private Location $location;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices={"Draft", "Approve"})
     * @JMS\SerializedName("status")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="status", type="string", enum={"Draft", "Approve"}, description="The status of the sale. Possible values: Draft, Approve.")
     */
    private string $status;

    public function __construct()
    {
        // Ensure the status property is initialized
        $this->status = 'Draft';

        // Initialize the product collection
        $this->products = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("products")
     * @JMS\Groups({"sale:read"})
     * @OA\Property(description="The products associated with the sale.",)
     * @OA\Property(ref=@Model(type=SaleProduct::class))
     * @return Collection|SaleProduct[]
     */
    public function getProducts(): ?Collection
    {
        return $this->products ?? null;
    }

    public function addProduct(SaleProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSale($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // Set the owning side to null
            $product->setSale(null);
        }

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("id")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(description="The unique identifier of the sale.",)
     * @OA\Property(type="integer")
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("customer")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(description="The customer associated with the sale.",)
     * @OA\Property(ref=@Model(type=Customer::class))
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void {
        $this->customer = $customer;
    }

//    public function setCustomer(?Customer $customer): self
//    {
//        $this->customer = $customer;
//
//        return $this;
//    }

//    /**
//     * @JMS\VirtualProperty
//     * @JMS\SerializedName("product")
//     * @JMS\Groups({"sale:read"})
//     * @JMS\MaxDepth(1)
//     * @OA\Property(description="The product associated with the sale.",)
//     * @OA\Property(ref=@Model(type=Product::class))
//     */
//    public function getProduct(): ?Product
//    {
//        return $this->product;
//    }

//    public function setProduct(Product $product): self
//    {
//        $this->product = $product;
//
//        return $this;
//    }

//    /**
//     * @JMS\VirtualProperty
//     * @JMS\SerializedName("price")
//     * @JMS\Groups({"sale:read", "sale:write"})
//     * @OA\Property(description="The price of the sale.",)
//     * @OA\Property(type="number")
//     */
//    public function getPrice(): ?float
//    {
//        return $this->price;
//    }

//    /**
//     * Set the price of the sale.
//     *
//     * @param float|null $price
//     * @return $this
//     */
//    public function setPrice(?float $price): self
//    {
//        $this->price = $price;
//
//        return $this;
//    }


    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("location")
     * @JMS\Groups({"sale:read"})
     * @JMS\MaxDepth(1)
     * @OA\Property(description="The location associated with the sale.",)
     * @OA\Property(ref=@Model(type=Location::class))
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

//    /**
//     * @JMS\VirtualProperty
//     * @JMS\SerializedName("quantity")
//     * @JMS\Groups({"sale:read", "sale:write"})
//     * @OA\Property(description="The quantity of the product sold.",)
//     * @OA\Property(type="integer")
//     */
//    public function getQuantity(): ?int
//    {
//        return $this->quantity;
//    }

//    public function setQuantity(int $quantity): self
//    {
//        $this->quantity = $quantity;
//
//        return $this;
//    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("status")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(description="The status of the sale. Possible values: Draft, Approve.",)
     * @OA\Property(type="string")
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("stock")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(description="The stock associated with the sale.",)
     * @OA\Property(ref=@Model(type=Stock::class))
     */
    public function getStock(): Sale
    {
        return $this;
    }

    public function addSale(Sale $sale)
    {
        $this->sales[] = $sale;
    }
}
