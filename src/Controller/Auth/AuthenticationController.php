<?php


namespace App\Controller\Auth;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/sign-up",name="sign_up")
     * @return Response
     */
    public function displaySignUpAction():Response
    {
        return $this->render('auth\sign_up.html.twig');
    }
}