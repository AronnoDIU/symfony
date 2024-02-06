<?php

// src/Entity/Location.php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 * @OA\Schema(
 *     title="Location",
 *     description="Location entity"
 * )
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\SerializedName("id")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="id", type="integer", description="The unique identifier of the location.")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The name cannot be blank.")
     * @Assert\Length(max=255, maxMessage="The name cannot be longer than {{ limit }} characters.")
     * cascade={"persist", "merge"},
     * @JMS\SerializedName("name")
     * @JMS\Groups({"sale:read", "sale:write"})
     * @OA\Property(property="name", type="string", description="The name of the location.")
     */
    private ?string $name;

    public function __construct()
    {
        $this->name = ''; // Initialize the name property
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

    public function __toString()
    {
        return $this->name;
    }
}
