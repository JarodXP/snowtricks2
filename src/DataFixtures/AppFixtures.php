<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        /////////////// MEDIAS //////////////////////

        $avatarReveolte = new Media();

        $avatarReveolte->setFileName('avatar.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('avatar');

        $manager->persist($avatarReveolte);

        $avatarWawa = new Media();

        $avatarWawa->setFileName('avatar2.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('avatar');

        $manager->persist($avatarWawa);

        $ollieMainImage = new Media();

        $ollieMainImage->setFileName('ollie.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieMainImage);

        $ollieImage2 = new Media();

        $ollieImage2->setFileName('ollie2.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieImage2);

        $ollieImage3 = new Media();

        $ollieImage3->setFileName('ollie3.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieImage3);

        $ollieImage4 = new Media();

        $ollieImage4->setFileName('ollie4.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieImage4);

        $ollieImage5 = new Media();

        $ollieImage5->setFileName('ollie5.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieImage5);

        $ollieVideo = new Media();

        $ollieVideo->setFileName('snowboard.mp4')
            ->setMimeType('video/mp4')
            ->setAlt('ollie video');

        $manager->persist($ollieVideo);

        $ollieImage6 = new Media();

        $ollieImage6->setFileName('ollie6.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('ollie');

        $manager->persist($ollieImage6);


        $nollieMainImage = new Media();

        $nollieMainImage->setFileName('nollie.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('nollie');

        $manager->persist($nollieMainImage);

        $switchMainImage = new Media();

        $switchMainImage->setFileName('switch-ollie.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('switch ollie');

        $manager->persist($switchMainImage);

        $indyMainImage = new Media();

        $indyMainImage->setFileName('indy-grab1.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Indy Grab');

        $manager->persist($indyMainImage);

        /////////////// USERS //////////////////////

        $reveolte = new User();

        $reveolte->setEmail('reveolte@gmail.com')
            ->setUsername('Reveolte')
            ->setPassword($this->passwordEncoder->encodePassword(
                $reveolte,'azerty'))
            ->setFirstName('André')
            ->setLastName('Nonyme')
            ->setAvatar($avatarReveolte)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($reveolte);

        $wawa = new User();

        $wawa->setEmail('gregory.barile@gmail.com')
            ->setUsername('Wawa')
            ->setPassword($this->passwordEncoder->encodePassword(
                $wawa,'azerty'))
            ->setFirstName('Jérôme')
            ->setLastName('Ambon')
            ->setAvatar($avatarWawa);

        $manager->persist($wawa);

        /////////////// GROUPS //////////////////////

        $straightAir = new TrickGroup();

        $straightAir->setName('Straight Airs')
            ->setDescription('Up in the Air!');

        $manager->persist($straightAir);

        $grabs = new TrickGroup();

        $grabs->setName('Grabs')
            ->setDescription('Grab your board!');

        $manager->persist($grabs);

        /////////////// TRICKS //////////////////////

        $ollie = new Trick();

        $ollie->setName('Ollie')
            ->setAuthor($reveolte)
            ->setDescription('An ollie is the most basic trick in snowboarding, 
            and refers to jumping the board all the way off the ground.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($ollieMainImage)
            ->addMedia($ollieImage2)
            ->addMedia($ollieImage3)
            ->addMedia($ollieImage4)
            ->addMedia($ollieImage5)
            ->addMedia($ollieVideo)
            ->addMedia($ollieImage6);

        $manager->persist($ollie);

        $nollie = new Trick();

        $nollie->setName('Nollie')
            ->setAuthor($reveolte)
            ->setDescription('The reverse to ollie is called nollie or nose ollie where the player has to use
             his front foot to pop the board while using his back foot to control and guide the board in air to land safely.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($nollieMainImage);

        $manager->persist($nollie);

        $switch = new Trick();

        $switch->setName('Switch Ollie')
            ->setAuthor($wawa)
            ->setDescription('In case of switch ollie, the player has to perform an ollie while switching
             his position on the snowboard reverse his both feet positions.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($switchMainImage);

        $manager->persist($switch);

        $indy = new Trick();

        $indy->setName('Indy Grab')
            ->setAuthor($wawa)
            ->setDescription('In this trick, when a player is in the air, he has to bend down on the board
             and grab the toe edge of the board between his / her two legs or mostly the middle edge of the board using
              his/her back hand at that moment. Before landing, he/she has to leave the board.')
            ->setStatus(true)
            ->setTrickGroup($grabs)
            ->setMainImage($indyMainImage);

        $manager->persist($indy);

        /////////////// COMMENTS //////////////////////

        $comment1 = new Comment();

        $comment1->setContent('J\'adore ce trick!')
            ->setTrick($ollie)
            ->setUser($wawa);

        $manager->persist($comment1);

        $comment2 = new Comment();

        $comment2->setContent('Ne pas oublier de lever la planche en sautant')
            ->setTrick($ollie)
            ->setUser($reveolte);

        $manager->persist($comment2);

        $manager->flush();
    }
}