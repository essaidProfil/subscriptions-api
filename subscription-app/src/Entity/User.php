<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"contacts","list","details"})
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Groups({"list","details"})
     */
    protected string $email;

    /**
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Serializer\SerializedName("firstName")
     * @Serializer\Groups({"list","details"})
     */
    protected ?string $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Serializer\SerializedName("lastName")
     * @Serializer\Groups({"list","details"})
     */
    protected ?string $lastName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="users", fetch="EAGER")
     * @ORM\JoinTable(name="user_role")
     * @Serializer\Groups({"details"})
     */
    private Collection $roleEntities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscription", mappedBy="user", orphanRemoval=true)
     */
    private Collection $subscriptions;

    public function __construct()
    {
        $this->roleEntities = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);
        return $this;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return User
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return User
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Return role in string format for security raisons
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

    /**
     * @param Role $role
     * @return User
     */
    public function addRole(Role $role): self
    {
        if (!$this->roleEntities->contains($role)) {
            $this->roleEntities->add($role);
        }
        return $this;
    }

    /**
     * @param Role $role
     * @return User
     */
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
