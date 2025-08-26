<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\PriceOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PriceOptionFixtures extends Fixture
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

        // --- Product 1 ---
        $product1 = new Product();
        $product1->setName('Premium Subscription');
        $manager->persist($product1);

        $option1 = new PriceOption();
        $option1->setProduct($product1);
        $option1->setCode('PREMIUM_MONTHLY');
        $option1->setAmountCents(999); // 9.99 EUR
        $option1->setCurrency('EUR');
        $manager->persist($option1);

        $option2 = new PriceOption();
        $option2->setProduct($product1);
        $option2->setCode('PREMIUM_YEARLY');
        $option2->setAmountCents(9999); // 99.99 EUR
        $option2->setCurrency('EUR');
        $manager->persist($option2);

        // --- Product 2 ---
        $product2 = new Product();
        $product2->setName('Basic Subscription');
        $manager->persist($product2);

        $option3 = new PriceOption();
        $option3->setProduct($product2);
        $option3->setCode('BASIC_MONTHLY');
        $option3->setAmountCents(499); // 4.99 EUR
        $option3->setCurrency('EUR');
        $manager->persist($option3);

        $manager->flush();
    }
}
