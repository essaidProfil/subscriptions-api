<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\PriceOption;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Subscription business logic.
 */
class SubscriptionService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * Subscribe a user to a price option for a period based on option code.
     * - monthly => +1 month
     * - yearly  => +1 year
     * - default => open-ended
     *
     * @param User $user The subscribing user.
     * @param PriceOption $priceOption Selected pricing option.
     * @return Subscription The new persisted subscription.
     */
    public function subscribe(
        User $user,
        PriceOption $priceOption
    ): Subscription {
        $now = new \DateTimeImmutable();

        $subscription = new Subscription();
        $subscription->setUser($user)
            ->setPriceOption($priceOption)
            ->setStartedAt($now);

        switch (strtolower($priceOption->getCode())) {
        case 'monthly':
            $subscription->setEndsAt($now->modify('+1 month'));
            break;
        case 'yearly':
            $subscription->setEndsAt($now->modify('+1 year'));
            break;
        default:
            // open-ended (lifetime)
            $subscription->setEndsAt(null);
            break;
        }

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $subscription;
    }


    /**
     * Cancel a subscription (remains valid until endsAt).
     *
     * @param Subscription $subscription The subscription to cancel.
     * @return Subscription The updated subscription.
     */
    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->cancel();
        $this->entityManager->flush();
        return $subscription;
    }

    /**
     * List active subscriptions for a given user.
     *
     * @param User $user The user whose active subscriptions we want.
     * @return Subscription[] Array of active subscriptions.
     */
    public function getActiveSubscriptions(User $user): array
    {
        $activeSubscriptions = [];
        foreach ($user->getSubscriptions() as $subscription) {
            if ($subscription->isActive()) {
                $activeSubscriptions[] = $subscription;
            }
        }
        return $activeSubscriptions;
    }
}
