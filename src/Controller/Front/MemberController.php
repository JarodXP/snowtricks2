<?php


namespace App\Controller\Front;


use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        dump($user);

        //Creates the form & handles request
        $formProfile = $this->createForm(UserProfileType::class,$user);

        $formProfile->handleRequest($request);

        //Checks if data has been submitted & registers updates
        if($formProfile->isSubmitted() && $formProfile->isValid()){
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($user);
            $manager->flush();

            //Renders the filled-in form
            return $this->redirectToRoute('member-profile',['username'=>$user->getUsername()]);
        }

        //If no data has been submitted, renders the blank form
        return $this->render('front/member_profile.html.twig',[
            'formProfile' => $formProfile->createView(),
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