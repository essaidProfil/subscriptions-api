<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Repository\PriceOptionRepository;

#[ORM\Table(name: "price_option")]
#[ORM\Entity(repositoryClass: PriceOptionRepository::class)]
class PriceOption
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Serializer\Groups(["details"])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "options")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Serializer\Groups(["details"])]
    private Product $product;

    #[ORM\Column(type: "string", length: 64)]
    #[Serializer\Groups(["details"])]
    private string $code;

    #[ORM\Column(type: "integer")]
    #[Serializer\Groups(["details"])]
    private int $amountCents;

    #[ORM\Column(type: "string", length: 3)]
    #[Serializer\Groups(["details"])]
    private string $currency = 'EUR';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = strtolower($code);
        return $this;
    }

    public function getAmountCents(): int
    {
        return $this->amountCents;
    }

    public function setAmountCents(int $amountCents): self
    {
        $this->amountCents = $amountCents;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = strtoupper($currency);
        return $this;
    }
}
