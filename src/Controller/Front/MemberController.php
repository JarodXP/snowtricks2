<?php


namespace App\Controller\Front;

use App\CustomServices\AvatarUploader;
use App\Entity\Media;
use App\Entity\User;
use App\Form\UserProfileType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class MemberController extends AbstractController
{
    /**
     * @Route("/member/{username}", name="member-profile")
     * @param Request $request
     * @param AvatarUploader $uploader
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function profileFormAction(Request $request, AvatarUploader $uploader, TokenGeneratorInterface $tokenGenerator)
    {
        //Gets the current user.
        //Doctrine is used to create a User object different from the session User before form validation
        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['username' => $this->getUser()->getUsername()]);

        //Creates a new token and registers it into the database
        $token = $tokenGenerator->generateToken();

        $manager = $this->getDoctrine()->getManager();

        try {
            $user->setResetToken($token);
            $manager->flush();
        } catch (Exception $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        //Creates the form & handles request
        $formProfile = $this->createForm(UserProfileType::class, $user);

        $formProfile->handleRequest($request);

        //Checks if data has been submitted & registers updates
        if ($formProfile->isSubmitted() && $formProfile->isValid()) {

            //Gets the avatar file
            $avatarFile = $formProfile->get('avatar')->getData();

            if (!is_null($avatarFile)) {

                //Gets the current avatar to remove it after new one is set
                $formerAvatar = $this->getDoctrine()
                    ->getRepository(Media::class)
                    ->findOneBy(['id' => $user->getAvatar()]);

                // Creates a new Media Entity for the avatar from the uploaded file
                try {
                    $newAvatar = $uploader->createAvatarMedia($avatarFile);

                    //updates the avatar media in the user entity
                    $user->setAvatar($newAvatar);

                    //Registers the new avatar
                    $manager->persist($newAvatar);

                } catch (FileException $e) {
                    $this->addFlash('danger', $e->getMessage());
                }

                //removes the former avatar file if not default avatar
                if (!is_null($formerAvatar)) {
                    unlink($this->getParameter('avatars_directory').'/'.$formerAvatar->getFileName());
                    $manager->remove($formerAvatar);
                }
            }

            //Registers the user
            $manager->persist($user);

            //Syncs the database
            $manager->flush();

            //Renders the filled-in form
            return $this->redirectToRoute('member-profile', ['username'=>$user->getUsername()]);
        }

        //If no data has been submitted, renders the blank form
        return $this->render('front/member_profile.html.twig', [
            'formProfile' => $formProfile->createView(),
            'user' => $user,
        ]);
    }
}
