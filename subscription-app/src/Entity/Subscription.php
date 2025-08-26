<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Repository\SubscriptionRepository;

#[ORM\Table(name: "subscription")]
#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Serializer\Groups(["list", "details"])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "subscriptions")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Serializer\Groups(["details"])]
    private User $user;

    #[ORM\ManyToOne(targetEntity: PriceOption::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(["details"])]
    private PriceOption $priceOption;

    #[ORM\Column(type: "datetime_immutable")]
    #[Serializer\Groups(["details"])]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    #[Serializer\Groups(["details"])]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\Column(type: "boolean")]
    #[Serializer\Groups(["details"])]
    private bool $isCancelled = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    #[Serializer\Groups(["details"])]
    private bool $autoRenew = false;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Serializer\Groups(["details"])]
    private ?string $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPriceOption(): PriceOption
    {
        return $this->priceOption;
    }

    public function setPriceOption(PriceOption $priceOption): self
    {
        $this->priceOption = $priceOption;
        return $this;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function getIsCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function isActive(): bool
    {
        $now = new \DateTimeImmutable();
        return !$this->isCancelled && ($this->endsAt === null || $this->endsAt > $now);
    }

    public function cancel(): self
    {
        $this->isCancelled = true;
        return $this;
    }

    public function setIsCancelled(bool $isCancelled): self
    {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    public function isAutoRenew(): bool
    {
        return $this->autoRenew;
    }

    public function setAutoRenew(bool $autoRenew): self
    {
        $this->autoRenew = $autoRenew;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }
}
