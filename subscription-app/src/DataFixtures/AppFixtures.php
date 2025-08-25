<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Product;
use App\Entity\PriceOption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $om): void
    {
        $admin = $om->getRepository(Role::class)->findOneBy(['code' => 'ROLE_ADMIN']);
        $userRole = $om->getRepository(Role::class)->findOneBy(['code' => 'ROLE_USER']);
        $user = new User();
        $user->setEmail('admin@example.com')->setFirstName('Admin')->setLastName('Root');
        $user->setPassword($this->$userPasswordHasher->hashPassword($userPasswordHasher, 'adminpass'));
        if ($admin) $userPasswordHasher->addRole($admin);
        if ($userRole) $userPasswordHasher->addRole($userRole);
        $om->persist($userPasswordHasher);
        $product = new Product();
        $product->setName('Pro Plan');
        $om->persist($product);
        foreach ([['monthly', 9900, 'EUR'], ['yearly', 99000, 'EUR']] as [$code, $amount, $currency]) {
            $priceOption = new PriceOption();
            $priceOption->setProduct($product)->setCode($code)->setAmountCents($amount)->setCurrency($currency);
            $om->persist($priceOption);
        }
        $om->flush();
    }
}
