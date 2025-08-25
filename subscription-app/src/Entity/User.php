<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: "user")]
#[ORM\Entity(repositoryClass: App\Repository\UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Serializer\Groups(["contacts", "list", "details"])]
    protected ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    #[Serializer\Groups(["list", "details"])]
    protected string $email;

    #[ORM\Column(type: "string")]
    protected string $password;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    #[Serializer\SerializedName("firstName")]
    #[Serializer\Groups(["list", "details"])]
    protected ?string $firstName = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    #[Serializer\SerializedName("lastName")]
    #[Serializer\Groups(["list", "details"])]
    protected ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "users", fetch: "EAGER")]
    #[ORM\JoinTable(name: "user_role")]
    #[Serializer\Groups(["details"])]
    private Collection $roleEntities;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Subscription::class, orphanRemoval: true)]
    private Collection $subscriptions;

    public function __construct()
    {
        $this->roleEntities = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Retourne les rôles en string pour Symfony Security
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roleCodes = [];
        foreach ($this->roleEntities as $role) {
            $roleCodes[] = strtoupper($role->getCode());
        }
        $roleCodes[] = 'ROLE_USER';
        return array_values(array_unique($roleCodes));
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoleEntities(): Collection
    {
        return $this->roleEntities;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roleEntities->contains($role)) {
            $this->roleEntities->add($role);
        }
        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roleEntities->removeElement($role);
        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function eraseCredentials(): void
    {
    }
}
