<?php


namespace App\Controller\Front;


use App\Entity\Media;
use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/member/{username}", name="member-profile")
     */
    public function profileFormAction(Request $request, string $username)
    {
        //Gets the current user
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        //Creates the form & handles request
        $formProfile = $this->createForm(UserProfileType::class,$user);

        $formProfile->handleRequest($request);

        //Checks if data has been submitted & registers updates
        if($formProfile->isSubmitted() && $formProfile->isValid()){

            //Gets the avatar file
            $avatarFile = $formProfile->get('avatar')->get('avatar')->getData();

            //Sets the avatar filename
            if($avatarFile) {
                $formerAvatar = $this->getDoctrine()
                    ->getRepository(Media::class)
                    ->findOneBy(['id' => $user->getAvatar()]);

                $newAvatar = new Media();

                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                                                             $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                // Move the file to the directory where avatars are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger',$e->getMessage());
                }

                // updates the avatar properties
                $newAvatar->setFileName($newFilename);
                $newAvatar->setAlt('avatar');
                $newAvatar->setMimeType($avatarFile->getClientMimeType());

                //updates the avatar media in the user entity
                $user->setAvatar($newAvatar);

                //removes the former avatar file if not default avatar
                unlink($_SERVER['DOCUMENT_ROOT'].'media/avatars/'.$formerAvatar->getFileName());
            }

            //Syncs the database
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($user);
            $manager->remove($formerAvatar);
            $manager->flush();

            //Renders the filled-in form
            return $this->redirectToRoute('member-profile',['username'=>$user->getUsername()]);
        }

        //If no data has been submitted, renders the blank form
        return $this->render('front/member_profile.html.twig',[
            'formProfile' => $formProfile->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/member/profile-{username}", name="user-profile")
     */
    public function displayProfileAction(string $username)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['username' => $username]);

        return $this->render('front/user_profile.html.twig',[
            'user' => $user
        ]);
    }
}