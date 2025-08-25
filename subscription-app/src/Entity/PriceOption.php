<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="price_option")
 * @ORM\Entity(repositoryClass="App\Repository\PriceOptionRepository")
 */
class PriceOption
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"details"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="options")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @Serializer\Groups({"details"})
     */
    private Product $product;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Serializer\Groups({"details"})
     */
    private string $code;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"details"})
     */
    private int $amountCents;

    /**
     * @ORM\Column(type="string", length=3)
     *
     * @Serializer\Groups({"details"})
     */
    private string $currency = 'EUR';


    /** @return int|null */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** @return Product */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return PriceOption
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return PriceOption
     */
    public function setCode(string $code): self
    {
        $this->code = strtolower($code);
        return $this;
    }

    /**
     * @return int
     */
    public function getAmountCents(): int
    {
        return $this->amountCents;
    }

    /**
     * @param int $amountCents
     * @return PriceOption
     */
    public function setAmountCents(int $amountCents): self
    {
        $this->amountCents = $amountCents;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return PriceOption
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = strtoupper($currency);
        return $this;
    }
}
