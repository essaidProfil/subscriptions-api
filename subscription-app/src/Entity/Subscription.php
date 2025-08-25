<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"list","details"})
     */
    protected ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Serializer\Groups({"details"})
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PriceOption")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"details"})
     */
    private PriceOption $priceOption;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Groups({"details"})
     */
    private \DateTimeImmutable $startedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Serializer\Groups({"details"})
     */
    private ?\DateTimeImmutable $endsAt = null;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"details"})
     */
    private bool $isCancelled = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Subscription
     */
    public function setUser(User $user): self
    {
        $this->user = $user; return $this;
    }

    /**
     * @return PriceOption
     */
    public function getPriceOption(): PriceOption
    {
        return $this->priceOption;
    }

    /**
     * @param PriceOption $priceOption
     * @return Subscription
     */
    public function setPriceOption(PriceOption $priceOption): self
    {
        $this->priceOption = $priceOption;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTimeImmutable $startedAt
     * @return Subscription
     */
    public function setStartedAt(\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    /**
     * @param \DateTimeImmutable|null $endsAt
     * @return Subscription
     */
    public function setEndsAt(?\DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCancelled(): bool
    {
        return $this->isCancelled;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $now = new \DateTimeImmutable();
        return !$this->isCancelled && ($this->endsAt === null || $this->endsAt > $now);
    }

    /**
     * @return Subscription
     */
    public function cancel(): self
    {
        $this->isCancelled = true;
        return $this;
    }
}
