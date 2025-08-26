<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ([['ROLE_USER', 'User'], ['ROLE_ADMIN', 'Administrator']] as [$code, $name]) {
            $role = new Role();
            $role->setCode($code)->setName($name);
            $manager->persist($role);
        }
        $manager->flush();
    }
}
