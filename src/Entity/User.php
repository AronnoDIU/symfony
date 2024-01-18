<?php

// src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @method string getUserIdentifier()
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Username cannot be blank.")
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Password cannot be blank.")
     * @Assert\Length(min=6, minMessage="Password should be at least {{ limit }} characters.")
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Email cannot be blank.")
     * @Assert\Email(message="Invalid email address.")
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string the hashed password for this user
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function setRoles(array $array)
    {
        $this->roles = $array;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        return ''; // return empty string
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function hasRole(string $string)
    {
        // TODO: Implement hasRole() method.
    }
}
