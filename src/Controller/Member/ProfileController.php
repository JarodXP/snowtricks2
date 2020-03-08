<?php


namespace App\Controller\Member;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/member/profile-{username}", name="user-profile")
     */
    public function displayProfileAction(string $username)
    {
        return $this->render('member/user_profile.html.twig');
    }
}