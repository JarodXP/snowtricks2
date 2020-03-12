<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setEmail('reveolte@gmail.com')
            ->setUsername('Reveolte')
            ->setPassword('azerty')
            ->setFirstName('André')
            ->setLastName('Nonyme')
            ->setRoles([]);

        $manager->persist($user);

        $manager->flush();
    }
}
