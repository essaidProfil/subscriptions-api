<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\PriceOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PREMIUM_MONTHLY_REF = 'premium-monthly';

    public function load(ObjectManager $manager): void
    {
        $premium = new Product();
        $premium->setName('Premium Subscription');
        $manager->persist($premium);

        $premiumMonthly = new PriceOption();
        $premiumMonthly->setProduct($premium);
        $premiumMonthly->setCode('PREMIUM_MONTHLY');
        $premiumMonthly->setAmountCents(999);
        $premiumMonthly->setCurrency('CAD');
        $manager->persist($premiumMonthly);

        $manager->flush();

        // référence pour SubscriptionFixtures
        $this->setReference(self::PREMIUM_MONTHLY_REF, $premiumMonthly);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
