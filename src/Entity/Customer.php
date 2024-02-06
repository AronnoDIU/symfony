<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @OA\Schema(
 *     title="Customer",
 *     description="Customer entity"
 * )
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"sale:read","sale:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the customer.")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The name cannot be blank.")
     * @JMS\SerializedName("name")
     * @JMS\Groups({"sale:read","sale:write"})
     * @OA\Property(property="name", type="string", description="The name of the customer.")
     */
    private ?string $name;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     * @JMS\SerializedName("enabled")
     * @JMS\Groups({"sale:read","sale:write"})
     * @OA\Property(property="enabled", type="boolean", description="Whether the customer is enabled.")
     */
    private ?bool $enabled;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The email cannot be blank.")
     * @Assert\Email(message="Invalid email address.")
     * @JMS\SerializedName("email")
     * @JMS\Groups({"sale:read","sale:write"})
     * @OA\Property(property="email", type="string", description="The email address of the customer.")
     */
    private ?string $email;

    public function __construct()
    {
        $this->enabled = true;
    }

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

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function __toString()
    {
        return $this->name . ' (' . $this->email . ')';
    }
}
