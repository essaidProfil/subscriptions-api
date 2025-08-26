<?php

namespace App\DataFixtures;

use App\Entity\PriceOption;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SubscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::ADMIN_REFERENCE);

        /** @var PriceOption $premiumMonthly */
        $premiumMonthly = $this->getReference(PriceOptionFixtures::PREMIUM_MONTHLY_REF);

        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setPriceOption($premiumMonthly);
        $subscription->setStartedAt(new \DateTimeImmutable());
        $subscription->setEndsAt((new \DateTimeImmutable())->modify('+1 month'));

        $manager->persist($subscription);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PriceOptionFixtures::class,
        ];
    }
}
