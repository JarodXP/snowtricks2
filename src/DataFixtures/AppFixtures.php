<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\CustomServices\SlugMaker;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private SlugMaker $slugMaker;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SlugMaker $slugMaker
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SlugMaker $slugMaker)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugMaker = $slugMaker;
    }

    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     * @throws Exception
     */
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

        $avatarJuju = new Media();

        $avatarJuju->setFileName('avatar3.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('avatar');

        $manager->persist($avatarJuju);

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

        $noseMainImage = new Media();

        $noseMainImage->setFileName('nose-grab.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Nose Grab');

        $manager->persist($noseMainImage);

        $muteMainImage = new Media();

        $muteMainImage->setFileName('mute-air.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Mute Grab');

        $manager->persist($muteMainImage);

        $melonMainImage = new Media();

        $melonMainImage->setFileName('melon-grab.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Melon Grab');

        $manager->persist($melonMainImage);

        $tripodMainImage = new Media();

        $tripodMainImage->setFileName('tripod.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Tripod');

        $manager->persist($tripodMainImage);

        $backflipMainImage = new Media();

        $backflipMainImage->setFileName('backflip.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('Backflip');

        $manager->persist($backflipMainImage);

        $mctwistMainImage = new Media();

        $mctwistMainImage->setFileName('mctwist.jpg')
            ->setMimeType('image/jpeg')
            ->setAlt('MacTwist');

        $manager->persist($mctwistMainImage);

        /////////////// USERS //////////////////////

        $reveolte = new User();

        $reveolte->setEmail('reveolte@gmail.com')
            ->setUsername('Reveolte')
            ->setPassword($this->passwordEncoder->encodePassword(
                $reveolte,
                'azerty'
            ))
            ->setFirstName('André')
            ->setLastName('Nonyme')
            ->setAvatar($avatarReveolte)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($reveolte);

        $wawa = new User();

        $wawa->setEmail('gregory.barile@gmail.com')
            ->setUsername('Wawa')
            ->setPassword($this->passwordEncoder->encodePassword(
                $wawa,
                'azerty'
            ))
            ->setFirstName('Jérôme')
            ->setLastName('Ambon')
            ->setAvatar($avatarWawa);

        $manager->persist($wawa);

        $juju = new User();

        $juju->setEmail('juju@gmail.com')
            ->setUsername('Juju')
            ->setPassword($this->passwordEncoder->encodePassword(
                $wawa,
                'azerty'
            ))
            ->setFirstName('Alfred')
            ->setLastName('Bruti')
            ->setAvatar($avatarJuju);

        $manager->persist($juju);

        /////////////// GROUPS //////////////////////

        $straightAir = new TrickGroup();

        $straightAir->setName('Straight Airs')
            ->setDescription('Up in the Air!');

        $manager->persist($straightAir);

        $grabs = new TrickGroup();

        $grabs->setName('Grabs')
            ->setDescription('Grab your board!');

        $manager->persist($grabs);

        $flips = new TrickGroup();

        $flips->setName('Flips')
            ->setDescription('Flips and inverted rotations');

        $manager->persist($flips);

        $slides = new TrickGroup();

        $slides->setName('Slides')
            ->setDescription('Let it slide');

        $manager->persist($slides);

        /////////////// TRICKS //////////////////////

        $ollie = new Trick();
        $ollie->setSlugMaker($this->slugMaker);

        $ollie->setName('Ollie')
            ->setAuthor($reveolte)
            ->setDescription('An ollie is the most basic trick in snowboarding, 
            and refers to jumping the board all the way off the ground.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($ollieMainImage)
            ->addMedia($ollieMainImage)
            ->addMedia($ollieImage2)
            ->addMedia($ollieImage3)
            ->addMedia($ollieImage4)
            ->addMedia($ollieImage5)
            ->addMedia($ollieVideo)
            ->addMedia($ollieImage6);

        $manager->persist($ollie);

        $nollie = new Trick();
        $nollie->setSlugMaker($this->slugMaker);

        $nollie->setName('Nollie')
            ->setAuthor($reveolte)
            ->setDescription('The reverse to ollie is called nollie or nose ollie where the player has to use
             his front foot to pop the board while using his back foot to control and guide the board in air to land safely.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($nollieMainImage);

        $manager->persist($nollie);

        $switch = new Trick();
        $switch->setSlugMaker($this->slugMaker);

        $switch->setName('Switch Ollie')
            ->setAuthor($wawa)
            ->setDescription('In case of switch ollie, the player has to perform an ollie while switching
             his position on the snowboard reverse his both feet positions.')
            ->setStatus(true)
            ->setTrickGroup($straightAir)
            ->setMainImage($switchMainImage);

        $manager->persist($switch);

        $indy = new Trick();
        $indy->setSlugMaker($this->slugMaker);

        $indy->setName('Indy Grab')
            ->setAuthor($wawa)
            ->setDescription('In this trick, when a player is in the air, he has to bend down on the board
             and grab the toe edge of the board between his / her two legs or mostly the middle edge of the board using
              his/her back hand at that moment. Before landing, he/she has to leave the board.')
            ->setStatus(true)
            ->setTrickGroup($grabs)
            ->setMainImage($indyMainImage);

        $manager->persist($indy);

        $noseGrab = new Trick();
        $noseGrab->setSlugMaker($this->slugMaker);

        $noseGrab->setName('Nose Grab')
            ->setAuthor($reveolte)
            ->setDescription('Unsurprisingly, this trick involves grabbing the nose of your board.
            As you jump in the air, straighten your back leg and lift up your front leg.
            This will bring the nose of your board towards you, and allow you to easily reach down with your 
            front hand and grab your nose.')
            ->setStatus(true)
            ->setTrickGroup($grabs)
            ->setMainImage($noseMainImage);

        $manager->persist($noseGrab);

        $backflip = new Trick();
        $backflip->setSlugMaker($this->slugMaker);

        $backflip->setName('Backflip')
            ->setAuthor($reveolte)
            ->setDescription('Flipping backwards (like a standing backflip) off of a jump.')
            ->setStatus(true)
            ->setTrickGroup($flips)
            ->setMainImage($backflipMainImage);

        $manager->persist($backflip);

        $mctwist = new Trick();
        $mctwist->setSlugMaker($this->slugMaker);

        $mctwist->setName('McTwist')
            ->setAuthor($reveolte)
            ->setDescription('A forward-flipping backside 540, performed in a halfpipe, quarterpipe, or similar obstacle. The rotation may continue beyond 540° (e.g., McTwist 720). The origin of this trick comes from vert ramp skateboarding, and was first performed on a skateboard by Mike McGill. ')
            ->setStatus(true)
            ->setTrickGroup($flips)
            ->setMainImage($mctwistMainImage);

        $manager->persist($mctwist);

        $melon = new Trick();
        $melon->setSlugMaker($this->slugMaker);

        $melon->setName('Melon Grab')
            ->setAuthor($reveolte)
            ->setDescription('An invert with a sad grab')
            ->setStatus(true)
            ->setTrickGroup($grabs)
            ->setMainImage($melonMainImage);

        $manager->persist($melon);

        $mute = new Trick();
        $mute->setSlugMaker($this->slugMaker);

        $mute->setName('Mute Air')
            ->setAuthor($reveolte)
            ->setDescription('The front hand grabs the toe edge either between the toes or in front of the front foot.[1] Variations include the Mute Stiffy, in which a mute grab is performed while straightening both legs, or alternatively, some snowboarders will grab mute and rotate the board frontside 90 degrees.')
            ->setStatus(true)
            ->setTrickGroup($grabs)
            ->setMainImage($muteMainImage);

        $manager->persist($mute);

        $tripod = new Trick();
        $tripod->setSlugMaker($this->slugMaker);

        $tripod->setName('Tripod')
            ->setAuthor($juju)
            ->setDescription('A slide performed where the rider\'s leading foot passes over the rail on approach, with their snowboard traveling perpendicular and trailing foot directly above the rail or other obstacle (like a tailslide). When performing a frontside bluntslide, the snowboarder is facing uphill. When performing a backside bluntslide, the snowboarder is facing downhill.')
            ->setStatus(true)
            ->setTrickGroup($slides)
            ->setMainImage($tripodMainImage);

        $manager->persist($tripod);

        /////////////// COMMENTS //////////////////////

        $randomContent = [
            'Awesome',
            'I love this trick',
            'Don\'t forget to raise the back of the board',
            'How do you achieve this?',
            'Thanks for the explanations',
            'I think you need some wings to achieve it',
            'Easy !',
            'It\'s also called the Jimmy Hendrix!',
            'Do you have a tip for this?',
            'I just lost my leg by doing it',
            'What\'s he secret?',
            'You have to turn your neck to the right and your bottom to the left',
            'Great stuff Key, I like the ways you get real specific with the techniques. keep it up, Im sure your site is going to be very usefull for alot of people. ',
            'Was wondering if you have any indepth tips for newbies starting jumps. i\'ve been having trouble going off a jump, air and then the landing. thanks ',
            'Before going through any particulars, start off by checking your body alignment. Shoulders hips in line with you board as you ride, with knees and ankles slightly flexed in a comfortable position (knees over toes). Maybe get a friend to film you riding towards them on a shallow gradient just to check everything is looking good.',
            'Think of the following 2 things when next trying them.
            1. front hand out, relaxed, over the nose of your board - keep it there on the run in, in the air and on the run out.
            2. Keep the head up looking forward. Focus on the lip, or object you want to jump over on the run in, as you reach this point transfer your focus point into the direction of travel and finally the landing.',
            'For style - I would recommend bending your knees to the tips of your board a little more, and then straightening up your back so you are more balanced on your board. ',
        ];

        $users = [$wawa, $juju, $reveolte];

        $tricks = [$ollie, $nollie, $switch, $indy, $noseGrab, $backflip, $mctwist, $melon, $mute, $tripod];

        foreach ($tricks as $trick) {
            for ($i = 1; $i < 11; $i++) {
                $comment = new Comment();
                $comment
                    ->setTrick($trick)
                    ->setUser($users[array_rand($users)])
                    ->setContent($randomContent[array_rand($randomContent)]);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
