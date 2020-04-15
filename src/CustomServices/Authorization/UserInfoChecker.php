<?php


namespace App\CustomServices\Authorization;


use App\Controller\SecurityController;
use App\Entity\User;
use App\Exception\RedirectException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class UserInfoChecker
{
    private SessionInterface $session;
    private RouterInterface $router;
    private EntityManagerInterface $manager;

    public function __construct(SessionInterface $session, RouterInterface $router, EntityManagerInterface $manager)
    {
        $this->session = $session;
        $this->router = $router;
        $this->manager = $manager;
    }

    /**
     * @param FormInterface $form
     * @return User
     * @throws RedirectException
     */
    public function checkForgotPasswordInfos(FormInterface $form):User
    {
        //Gets the form username
        $username = $form->get(SecurityController::USERNAME_FIELD)->getData();

        //Gets the user by his username
        $user = $this->manager->getRepository(User::class)->findOneBy([SecurityController::USERNAME_FIELD=>$username]);

        //Checks if user is unknown
        if ($user === null) {
            $this->session->getFlashBag()->add('error', 'Unknown user');

            throw new RedirectException($this->router->generate('app_forgotten_password'));
        }
        //Checks if form email address matches the user's one.
        else if(!($user->getEmail() == $form->get(SecurityController::EMAIL_FIELD)->getData())){
            $this->session->getFlashBag()
                ->add('error','This email doesn\'t match the one registered for '.$user->getUsername());

            throw new RedirectException($this->router->generate('app_forgotten_password'));
        }

        return $user;
    }
}