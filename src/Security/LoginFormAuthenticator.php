<?php

namespace App\Security;

use App\Entity\User;
use App\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $passwordEncoder;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder,FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->formFactory = $formFactory;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        //Gets the request from the loginFormType
        $loginForm = $this->formFactory->create(LoginFormType::class);
        $loginForm->handleRequest($request);

        //Sets credentials if form is valid
        if ($loginForm->isSubmitted() && $loginForm->isValid()){
            $credentials = [
                'username' => $loginForm->get('username')->getData(),
                'password' => $loginForm->get('password')->getData(),
            ];

            $request->getSession()->set(
                Security::LAST_USERNAME,
                $credentials['username']
            );

            return $credentials;
        }

        // Throws exception in case loginForm is invalid
        throw new CustomUserMessageAuthenticationException($loginForm->getErrors(true,true));
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return  new RedirectResponse($this->urlGenerator->generate('home'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
