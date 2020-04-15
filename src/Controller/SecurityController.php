<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use App\Security\LoginFormAuthenticator;
use Exception;
use LogicException;
use phpDocumentor\Reflection\Types\Self_;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Swift_Mailer;

class SecurityController extends AbstractController
{
    public const USERNAME_FIELD = 'username',
        EMAIL_FIELD = 'email',
        PASSWORD_FIELD = 'password',
        PASSWORD_CHECK_FIELD = 'password-check';

    /**
     * @Route("/auth/login", name="app_login")
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
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
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
                ));

            //Registers the new user in database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //Authenticates the new created user
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('auth/sign_up.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/auth/forgot-password", name="app_forgotten_password")
     * @return Response
     */
    public function forgottenPassword(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        //Creates the form to handle request.
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Gets the email address
            $username = $form->get(self::USERNAME_FIELD)->getData();

            //Gets the user by his username
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([self::USERNAME_FIELD=>$username]);

            //Checks if user is unknown
            if ($user === null) {
                $this->addFlash('error', 'Unknown user');

                return $this->redirectToRoute('app_forgotten_password');
            }
            //Checks if form email address matches the user's one.
            else if(!($user->getEmail() == $form->get(self::EMAIL_FIELD)->getData())){
                $this->addFlash('error','This email doesn\'t match the one registered for '.$username);

                return $this->redirectToRoute('app_forgotten_password');
            }

            //Creates a new token and registers it into the database
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em->flush();
            } catch (Exception $e) {
                $this->addFlash('warning', $e->getMessage());

                return $this->redirectToRoute('home');
            }

            //Creates the mail message through Swift
            $url = $this->generateUrl(
                'app_reset_password',
                ['token'=>$token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $message = (new Swift_Message('Forgot Password'))
            ->setFrom('gregory.barile@gmail.com')
            ->setTo($user->getEmail())
            ->setBody('<p>Hello '.$user->getUsername().'</p><p>You requested a password change, please click on the
 link below and follow the instructions: </p><p><a href="'.$url.'">'.$url.'</a></p>', 'text/html');

            //Sends the message
            $mailer->send($message);

            $this->addFlash('notice', 'Email sent');

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/forgot_password.html.twig',[
            'forgotForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/auth/reset-password/{token}",name="app_reset_password")
     * @return Response
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $resetForm = $this->createForm(ResetPasswordFormType::class);
        $resetForm->handleRequest($request);

        if($resetForm->isSubmitted() && $resetForm->isValid()){
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
        return $this->render('auth/reset_password.html.twig',[
            'resetForm' => $resetForm->createView(),
            'token' => $token
        ]);
    }
}
