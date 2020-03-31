<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/auth/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/sign_in.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/auth/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/auth/signup", name="app_signup")
     * @return Response
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if($request->isMethod('POST')){
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));

            //Checks if the 2 passwords field typed by the user are identical
            if($request->request->get('password') === $request->request->get('password-check')){
                $user->setPassword($passwordEncoder->encodePassword($request->request->get('password')));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/sign_up.html.twig');
    }

    /**
     * @Route("/auth/forgot-password", name="app_forgotten_password")
     * @return Response
     */
    public function forgottenPassword(): Response
    {
        return $this->render('auth/forgot_password.html.twig');
    }
}
