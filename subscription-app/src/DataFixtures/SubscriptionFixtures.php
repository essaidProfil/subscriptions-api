<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\PriceOption;
use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SubscriptionFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- User admin ---
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setFirstName('Admin');
        $user->setLastName('User');

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'adminpass');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        // --- Product 1 : Premium Subscription ---
        $premium = new Product();
        $premium->setName('Premium Subscription');
        $manager->persist($premium);

        $premiumMonthly = new PriceOption();
        $premiumMonthly->setProduct($premium);
        $premiumMonthly->setCode('PREMIUM_MONTHLY');
        $premiumMonthly->setAmountCents(999); // 9.99 CAD
        $premiumMonthly->setCurrency('CAD');
        $manager->persist($premiumMonthly);

        $premiumYearly = new PriceOption();
        $premiumYearly->setProduct($premium);
        $premiumYearly->setCode('PREMIUM_YEARLY');
        $premiumYearly->setAmountCents(9999); // 99.99 CAD
        $premiumYearly->setCurrency('CAD');
        $manager->persist($premiumYearly);

        // --- Product 2 : Basic Subscription ---
        $basic = new Product();
        $basic->setName('Basic Subscription');
        $manager->persist($basic);

        $basicMonthly = new PriceOption();
        $basicMonthly->setProduct($basic);
        $basicMonthly->setCode('BASIC_MONTHLY');
        $basicMonthly->setAmountCents(499); // 4.99 CAD
        $basicMonthly->setCurrency('CAD');
        $manager->persist($basicMonthly);

        // --- Subscription example ---
        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setPriceOption($premiumMonthly);
        $subscription->setStartedAt(new \DateTimeImmutable('now'));
        $subscription->setEndsAt((new \DateTimeImmutable('now'))->modify('+1 month'));
        $manager->persist($subscription);

        $manager->flush();
    }
}
