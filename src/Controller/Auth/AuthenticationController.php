<?php


namespace App\Controller\Auth;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/auth/sign-up",name="sign_up")
     * @return Response
     */
    public function displaySignUpAction():Response
    {
        return $this->render('auth\sign_up.html.twig');
    }

    /**
     * @Route("/auth/forgot-password",name="forgot-password")
     * @return Response
     */
    public function displayForgotPasswordAction():Response
    {
        return $this->render('auth\forgot_password.html.twig');
    }

    /**
     * @Route("/auth/reset-password",name="reset-password")
     * @return Response
     */
    public function displayResetPasswordAction():Response
    {
        return $this->render('auth\reset_password.html.twig');
    }
}