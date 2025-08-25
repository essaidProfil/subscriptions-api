<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Table(name: "role")]
#[ORM\Entity(repositoryClass: App\Repository\RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Serializer\Groups(["details"])]
    protected ?int $id = null;

    #[ORM\Column(type: "string", length: 64, unique: true)]
    #[Serializer\Groups(["details"])]
    private string $code;

    #[ORM\Column(type: "string", length: 128)]
    #[Serializer\Groups(["details"])]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "roleEntities")]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = strtoupper($code);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
