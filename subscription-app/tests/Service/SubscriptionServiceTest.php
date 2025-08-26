<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PriceOption;
use App\Entity\Subscription;
use App\Entity\User;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SubscriptionService.
 *
 * @internal
 */
#[CoversClass(SubscriptionService::class)]
#[Group('unit')]
final class SubscriptionServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    private SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->subscriptionService = new SubscriptionService($this->entityManager);
    }

    #[Test]
    public function subscribe_monthly_sets_ends_at_plus_one_month(): void
    {
        $user = $this->createMock(User::class);

        $monthlyOption = (new PriceOption())
            ->setCode('monthly')
            ->setAmountCents(1000)
            ->setCurrency('CAD');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $monthlyOption);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertNotNull($subscription->getEndsAt());
        $this->assertGreaterThan($subscription->getStartedAt(), $subscription->getEndsAt());
    }

    #[Test]
    public function subscribe_yearly_sets_ends_at_plus_one_year(): void
    {
        $user = $this->createMock(User::class);

        $yearlyOption = (new PriceOption())
            ->setCode('yearly')
            ->setAmountCents(1000)
            ->setCurrency('CAD');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $yearlyOption);

        $this->assertNotNull($subscription->getEndsAt());
        $this->assertGreaterThan($subscription->getStartedAt(), $subscription->getEndsAt());
    }

    #[Test]
    public function subscribe_open_ended_when_unknown_code(): void
    {
        $user = $this->createMock(User::class);

        $lifetimeOption = (new PriceOption())
            ->setCode('lifetime')
            ->setAmountCents(50000)
            ->setCurrency('CAD');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $lifetimeOption);

        $this->assertNull($subscription->getEndsAt(), 'Unknown code => open-ended');
    }

    #[Test]
    public function cancel_sets_is_cancelled_true(): void
    {
        $subscription = new Subscription();

        $this->entityManager->expects($this->once())->method('flush');

        $updated = $this->subscriptionService->cancel($subscription);

        $this->assertTrue($updated->getIsCancelled());
    }

    #[Test]
    public function get_active_subscriptions_filters_correctly(): void
    {
        $active = (new Subscription())
            ->setStartedAt(new \DateTimeImmutable('-1 day'))
            ->setEndsAt(new \DateTimeImmutable('+10 days'));

        $cancelled = (new Subscription())
            ->setStartedAt(new \DateTimeImmutable('-10 days'))
            ->setEndsAt(new \DateTimeImmutable('+10 days'))
            ->cancel();

        $expired = (new Subscription())
            ->setStartedAt(new \DateTimeImmutable('-10 days'))
            ->setEndsAt(new \DateTimeImmutable('-1 day'));

        $user = $this->createMock(User::class);
        $user->method('getSubscriptions')->willReturn(
            new ArrayCollection([$active, $cancelled, $expired])
        );

        $result = $this->subscriptionService->getActiveSubscriptions($user);

        $this->assertCount(1, $result);
        $this->assertSame($active, $result[0]);
    }
}
