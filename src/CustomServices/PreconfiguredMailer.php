<?php

declare(strict_types=1);


namespace App\CustomServices;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PreconfiguredMailer
 * Collection of preconfigured mails
 * @package App\CustomServices
 */
class PreconfiguredMailer
{
    protected UrlGeneratorInterface $generator;
    protected Swift_Mailer $mailer;
    protected SessionInterface $session;

    /**
     * PreconfiguredMailer constructor.
     * @param UrlGeneratorInterface $generator
     * @param Swift_Mailer $mailer
     * @param SessionInterface $session
     */
    public function __construct(UrlGeneratorInterface $generator, Swift_Mailer $mailer, SessionInterface $session)
    {
        $this->generator = $generator;
        $this->mailer = $mailer;
        $this->session = $session;
    }

    /**
     * Builds and sends the mail for SecurityController::signUp()
     * @param string $token
     * @param User $user
     */
    public function sendActivationMail(string $token, User $user)
    {
        //Builds the Url
        $url = $this->generator->generate(
            'app_confirm_registration',
            ['token'=>$token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        //Gives Swift the mail parameters
        $message = (new Swift_Message('Activate your account'))
            ->setFrom(['admin@snowtricks.com' => 'Admin Snowtricks'])
            ->setTo($user->getEmail())
            ->setBody('<h1>Confirm registration</h1>
                <p>Hello '.$user->getUsername().'</p>
                <p>Thank you for registering.</p>
                <p>To activate your account, you have click on the following link: </p>
                <p><a href="'.$url.'">'.$url.'</a></p>', 'text/html');

        $this->sendMessage($message);
    }

    /**
     * Builds and sends the mail for SecurityController::forgottenPassword()
     * @param string $token
     * @param User $user
     */
    public function sendForgottenPasswordMail(string $token, User $user)
    {
        //Creates the mail message through Swift
        $url = $this->generator->generate(
            'app_reset_password',
            ['token'=>$token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = (new Swift_Message('Forgot Password'))
            ->setFrom(['admin@snowtricks.com' => 'Admin Snowtricks'])
            ->setTo($user->getEmail())
            ->setBody('<p>Hello '.$user->getUsername().'</p><p>You requested a password change, please click on the
 link below and follow the instructions: </p><p><a href="'.$url.'">'.$url.'</a></p>', 'text/html');

        $this->sendMessage($message);
    }

    /**
     * Sends the message and notifies the user
     * @param Swift_Message $message
     */
    public function sendMessage(Swift_Message $message)
    {
        //Sends the message
        $this->mailer->send($message);

        $this->session->getFlashBag()->add('notice', 'Email sent');
    }
}
