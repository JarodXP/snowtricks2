<?php

declare(strict_types=1);


namespace App\Controller\Front;

use App\CustomServices\AvatarUploader;
use App\CustomServices\EntityRemover;
use App\Entity\Media;
use App\Entity\User;
use App\Form\UserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Class MemberController
 * @package App\Controller\Front
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/member/edit/{username}", name="member-profile")
     * @param Request $request
     * @param User $user
     * @param AvatarUploader $uploader
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function profileFormAction(Request $request, User $user, AvatarUploader $uploader, TokenGeneratorInterface $tokenGenerator)
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $manager = $this->getDoctrine()->getManager();

        //Creates a new token for password resetting
        $token = $tokenGenerator->generateToken();
        $user->setResetToken($token);

        //Creates the form & handles request
        $formProfile = $this->createForm(UserProfileType::class, $user, [
            'attr' => ['id' => 'profile-form']
        ]);

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

        //registers it into the database
        $manager->persist($user);
        $manager->flush();

        //If no data has been submitted, renders the blank form
        return $this->render('front/member_profile.html.twig', [
            'formProfile' => $formProfile->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/member/remove-account/{username}", name="remove-account")
     * @param Request $request
     * @param User $user
     * @param EntityRemover $remover
     * @return RedirectResponse
     */
    public function removeAccountAction(Request $request, User $user, EntityRemover $remover)
    {
        //Uses Security voter to grant access
        $this->denyAccessUnlessGranted('edit', $user);

        //Removes the user
        $removeResponse = $remover->removeEntity($request, $user, 'delete-user');

        //Adds a flash message
        $this->addFlash($removeResponse['flashType'], $removeResponse['message']);

        return $this->redirectToRoute('home');
    }
}
