<?php

// src/Entity/Transfer.php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Transfer\Product as TransferProduct;

/**
 * @ORM\Entity(repositoryClass=TransferRepository::class)
 * @OA\Schema(
 *      title="Transfer",
 *      description="Transfer entity"
 *  )
 */
class Transfer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the transfer.")
     */
    private ?int $id;

    /**
     * @ORM\OneToMany(targetEntity=TransferProduct::class, mappedBy="transfer", cascade={"persist"})
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(property="products", ref=@Model(type=TransferProduct::class), description="The products associated with the transfer.")
     */
    private Collection $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("App\Entity\Location")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(property="location", ref=@Model(type=Location::class), description="The location associated with the transfer.")
     */
    private ?Location $location;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices={"Draft", "Approve", "Cancel"}, message="Invalid status.")
     * @JMS\SerializedName("status")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(property="status", type="string", enum={"Draft", "Approve", "Cancel"}, description="The status of the transfer.")
     */
    private ?string $status;

    public function __construct()
    {
        // Ensure the status property is initialized with a default value
        $this->status = "Draft";

        // Ensure the products property is initialized with an empty ArrayCollection
        $this->products = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("id")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(description="The unique identifier of the transfer.")
     * @OA\Property(type="integer")
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("products")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(description="The products associated with the transfer.")
     * @OA\Property(ref=@Model(type=TransferProduct::class))
     * @return Collection|TransferProduct[]
     */
    public function getProducts(): ?Collection
    {
        return $this->products ?? new ArrayCollection();
    }

    public function addProduct(TransferProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setTransfer($this);
        }

        return $this;
    }

    public function removeProduct(TransferProduct $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getTransfer() === $this) {
                $product->setTransfer(null);
            }
        }

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("location")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @JMS\MaxDepth(1)
     * @OA\Property(description="The location associated with the transfer.")
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("status")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(description="The status of the transfer.")
     * @OA\Property(type="string")
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
