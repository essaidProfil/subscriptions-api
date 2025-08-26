<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\PriceOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PriceOptionFixtures extends Fixture implements DependentFixtureInterface
{
    public const PREMIUM_MONTHLY_REF = 'premium-monthly';
    public const PREMIUM_YEARLY_REF  = 'premium-yearly';
    public const BASIC_MONTHLY_REF   = 'basic-monthly';

    public function load(ObjectManager $manager): void
    {
        // Premium Subscription
        [$premiumMonthly, $premiumYearly] = $this->createProductWithOptions(
            $manager,
            'Premium Subscription',
            [
                ['code' => 'PREMIUM_MONTHLY', 'amount' => 999,  'currency' => 'CAD'],
                ['code' => 'PREMIUM_YEARLY',  'amount' => 9999, 'currency' => 'CAD'],
            ]
        );

        // Basic Subscription
        [$basicMonthly] = $this->createProductWithOptions(
            $manager,
            'Basic Subscription',
            [
                ['code' => 'BASIC_MONTHLY', 'amount' => 499, 'currency' => 'CAD'],
            ]
        );

        $manager->flush();

        // Ref for SubscriptionFixtures
        $this->setReference(self::PREMIUM_MONTHLY_REF, $premiumMonthly);
        $this->setReference(self::PREMIUM_YEARLY_REF, $premiumYearly);
        $this->setReference(self::BASIC_MONTHLY_REF, $basicMonthly);
    }

    /**
     * Create a product and its options
     * @return PriceOption[]
     */
    private function createProductWithOptions(ObjectManager $manager, string $name, array $options): array
    {
        $product = new Product();
        $product->setName($name);
        $manager->persist($product);

        $priceOptions = [];
        foreach ($options as $opt) {
            $option = new PriceOption();
            $option->setProduct($product);
            $option->setCode($opt['code']);
            $option->setAmountCents($opt['amount']);
            $option->setCurrency($opt['currency']);
            $manager->persist($option);

            $priceOptions[] = $option;
        }

        return $priceOptions;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
