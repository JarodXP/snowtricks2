<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomServices\Authorization\UserInfoChecker;
use App\CustomServices\PreconfiguredMailer;
use App\Exception\RedirectException;
use App\Form\ForgotPasswordType;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    public const USERNAME_FIELD = 'username',
        EMAIL_FIELD = 'email',
        PASSWORD_FIELD = 'password',
        PASSWORD_CHECK_FIELD = 'password-check';

    /**
     * @Route("/auth/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $form = $this->createForm(LoginFormType::class, $user);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/sign_in.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error]);
    }

    /**
     * @Route("/auth/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/auth/signup", name="app_signup")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGeneratorInterface $tokenGenerator
     * @param PreconfiguredMailer $customMailer
     * @return Response
     */
    public function signUp(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        PreconfiguredMailer $customMailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Sets the user's email and username
            $user->setEmail($form->get(self::EMAIL_FIELD)->getData());
            $user->setUsername($form->get(self::USERNAME_FIELD)->getData());

            // Encodes the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('passwordGroup')->get(self::PASSWORD_FIELD)->getData()
                )
            );

            //Creates a new token and registers it into the database
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);

            //Registers the new user in database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $customMailer->sendActivationMail($token, $user);

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/sign_up.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/auth/confirm-registration/{token}", name="app_confirm_registration")
     * @ParamConverter("user", options={"mapping": {"token":"resetToken"}})
     * @param User $user
     * @return Response|null
     */
    public function confirmRegistration(
        User $user
    ) {
        //Resets the token
        $user->setResetToken(null);

        //Activates the account in database
        $user->setActivated(true);

        //Sets the activated role
        $user->setRoles(['ROLE_ACTIVATED_USER']);

        //Registers in database
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        //Notifies the user
        $this->addFlash('notice', 'Congratulations! You are now part of the Snowtricks team!');

        //Redirects to login
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/auth/forgot-password", name="app_forgotten_password")
     * @param Request $request
     * @param PreconfiguredMailer $customMailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param UserInfoChecker $infoChecker
     * @return Response
     * @throws RedirectException
     */
    public function forgottenPassword(
        Request $request,
        PreconfiguredMailer $customMailer,
        TokenGeneratorInterface $tokenGenerator,
        UserInfoChecker $infoChecker
    ): Response {
        //Creates the form to handle request.
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Gets the email address
            $username = $form->get(self::USERNAME_FIELD)->getData();

            //Gets the user by his username
            $manager = $this->getDoctrine()->getManager();
            $user = $manager->getRepository(User::class)->findOneBy([self::USERNAME_FIELD=>$username]);

            //Checks user and email validity
            $infoChecker->checkForgotPasswordInfos($form);

            //Creates a new token and registers it into the database
            $token = $tokenGenerator->generateToken();

            $user->setResetToken($token);
            $manager->flush();

            $customMailer->sendForgottenPasswordMail($token, $user);

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/forgot_password.html.twig', [
            'forgotForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/auth/reset-password/{token}",name="app_reset_password")
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $resetForm = $this->createForm(ResetPasswordFormType::class);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Checks if the token is valid
            $user = $em->getRepository(User::class)->findOneBy(['resetToken'=>$token]);

            if ($user === null) {
                $this->addFlash('danger', 'Unknown token');

                return $this->redirectToRoute('home');
            }

            //reset the token
            $user->setResetToken(null);

            //sets the new password
            $user->setPassword($passwordEncoder->encodePassword($user, $resetForm->get('passwordGroup')
                ->get(self::PASSWORD_FIELD)->getData()));

            //Syncs with database
            $em->flush();

            $this->addFlash('notice', 'Your password has been updated');

            return $this->redirectToRoute('home');
        }

        //Displays the view
        return $this->render('auth/reset_password.html.twig', [
            'resetForm' => $resetForm->createView(),
            'token' => $token
        ]);
    }
}
