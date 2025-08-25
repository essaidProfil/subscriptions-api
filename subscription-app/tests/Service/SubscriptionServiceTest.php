<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PriceOption;
use App\Entity\Subscription;
use App\Entity\User;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\Exception;
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

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->subscriptionService = new SubscriptionService($this->entityManager);
    }

    /**
     * Ensure monthly subscription sets endsAt +1 month.
     *
     * @return void
     * @throws Exception
     */
    #[Test]
    public function test_subscribe_monthly_sets_ends_at_plus_one_month(): void
    {
        $user = $this->createMock(User::class);

        $monthlyOption = (new PriceOption())
            ->setCode('monthly')
            ->setAmountCents(1000)
            ->setCurrency('EUR');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Subscription::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $monthlyOption);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertNotNull($subscription->getEndsAt());
        $this->assertGreaterThan($subscription->getStartedAt(), $subscription->getEndsAt());
        $this->assertSame('monthly', $monthlyOption->getCode());
    }

    /**
     * Ensure yearly subscription sets endsAt +1 year.
     *
     * @return void
     * @throws Exception
     */
    #[Test]
    public function test_subscribe_yearly_sets_ends_at_plus_one_year(): void
    {
        $user = $this->createMock(User::class);

        $yearlyOption = (new PriceOption())
            ->setCode('yearly')
            ->setAmountCents(1000)
            ->setCurrency('EUR');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $yearlyOption);

        $this->assertNotNull($subscription->getEndsAt());
        $this->assertGreaterThan($subscription->getStartedAt(), $subscription->getEndsAt());
        $this->assertSame('yearly', $yearlyOption->getCode());
    }

    /**
     * Unknown code => open-ended subscription.
     *
     * @return void
     * @throws Exception
     */
    #[Test]
    public function test_subscribe_open_ended_when_unknown_code(): void
    {
        $user = $this->createMock(User::class);

        $lifetimeOption = (new PriceOption())
            ->setCode('lifetime')
            ->setAmountCents(50000)
            ->setCurrency('EUR');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $subscription = $this->subscriptionService->subscribe($user, $lifetimeOption);

        $this->assertNull($subscription->getEndsAt(), 'Unknown code => open-ended');
    }

    /**
     * Cancel should mark subscription as cancelled.
     *
     * @return void
     */
    #[Test]
    public function test_cancel_sets_is_cancelled_true(): void
    {
        $subscription = new Subscription();

        $this->entityManager->expects($this->once())->method('flush');

        $updated = $this->subscriptionService->cancel($subscription);

        $this->assertTrue($updated->getIsCancelled());
    }

    /**
     * getActiveSubscriptions filters cancelled and expired ones.
     *
     * @return void
     * @throws Exception
     */
    #[Test]
    public function test_get_active_subscriptions_filters_correctly(): void
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

        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method('getSubscriptions')->willReturn(
            new \Doctrine\Common\Collections\ArrayCollection([$active, $cancelled, $expired])
        );

        $result = $this->subscriptionService->getActiveSubscriptions($user);

        /** @var array<int, Subscription> $result */
        $this->assertCount(1, $result);
        $this->assertSame($active, $result[0]);
    }
}
