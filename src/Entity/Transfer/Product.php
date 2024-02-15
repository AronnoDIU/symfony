<?php

// src/Entity/Transfer/Product.php

namespace App\Entity\Transfer;

use App\Entity\Transfer;
use App\Repository\Transfer\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use App\Entity\Product as Original;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\Common\Annotations\DocLexer;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="transfer_product")
 * @OA\Schema(
 *     title="TransferProduct",
 *     description="Transfer\Product entity"
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the transfer\product.")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", cascade={"persist"})
     */
    private ?Original $original;

    /**
     * @@ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please enter a quantity.")
     * @JMS\SerializedName("quantity")
     * @JMS\Groups({"transfer:read", "transfer:write"})
     * @OA\Property(property="quantity", type="integer", description="The quantity of the product transferred.")
     */
    private ?int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transfer", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Transfer $transfer;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("original")
     * @JMS\Groups({"transfer:read"})
     * @OA\Property(description="The original associated with the transfer\product.",)
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTransfer(): ?Transfer
    {
        return $this->transfer;
    }

    public function setTransfer(Transfer $param): self
    {
        $this->transfer = $param;

        return $this;
    }
}
