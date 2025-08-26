<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProductRepository;

#[ORM\Table(name: "product")]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Serializer\Groups(["list", "details"])]
    protected ?int $id = null;

    #[ORM\Column(type: "string", length: 128)]
    #[Serializer\Groups(["list", "details"])]
    private string $name;

    #[ORM\OneToMany(
        mappedBy: "product",
        targetEntity: PriceOption::class,
        cascade: ["persist"],
        orphanRemoval: true
    )]
    #[Serializer\Groups(["details"])]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, PriceOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }
}
