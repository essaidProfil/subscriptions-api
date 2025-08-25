<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"list","details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128)
     *
     * @Serializer\Groups({"list","details"})
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PriceOption",
     *      mappedBy="product", cascade={"persist"},
     *      orphanRemoval=true)
     *
     * @Serializer\Groups({"details"})
     */
    private Collection $options;


    public function __construct()
    {
        $this->options = new ArrayCollection();
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
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
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
