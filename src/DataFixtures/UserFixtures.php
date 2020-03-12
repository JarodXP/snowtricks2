<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $avatar = new Media();

        $avatar->setFileName('avatar.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('avatar');

        $user = new User();

        $user->setEmail('reveolte@gmail.com')
            ->setUsername('Reveolte')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,'azerty'))
            ->setFirstName('André')
            ->setLastName('Nonyme')
            ->setAvatar($avatar)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }
}
