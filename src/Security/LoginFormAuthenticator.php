<?php

declare(strict_types=1);

namespace App\Security;

use App\Controller\SecurityController;
use App\Entity\User;
use App\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

/**
 * Class LoginFormAuthenticator
 * @package App\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private UserPasswordEncoderInterface $passwordEncoder;
    private FormFactoryInterface $formFactory;

    /**
     * LoginFormAuthenticator constructor.
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->formFactory = $formFactory;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      return [
     *          'username' => $request->request->get('_username'),
     *          'password' => $request->request->get('_password'),
     *      ];
     *
     * Or for an API token that's on a header, you might use:
     *
     *      return ['api_key' => $request->headers->get('X-API-TOKEN')];
     *
     * @param Request $request
     * @return mixed Any non-null value
     *
     */
    public function getCredentials(Request $request)
    {
        //Gets the request from the loginFormType
        $loginForm = $this->formFactory->create(LoginFormType::class);
        $loginForm->handleRequest($request);

        //Sets credentials if form is valid
        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $credentials = [
                SecurityController::USERNAME_FIELD => $loginForm->get(SecurityController::USERNAME_FIELD)->getData(),
                SecurityController::PASSWORD_FIELD => $loginForm->get(SecurityController::PASSWORD_FIELD)->getData(),
            ];

            $request->getSession()->set(
                Security::LAST_USERNAME,
                $credentials[SecurityController::USERNAME_FIELD]
            );

            return $credentials;
        }

        //Gets all the errors
        $errors = $loginForm->getErrors(true, true);
        $errorMessage='';

        //Concatenates all the error messages
        foreach ($errors as $error) {
            $errorMessage .= $error->getMessage().'</br>';
        }

        // Throws exception in case loginForm is invalid
        throw new CustomUserMessageAuthenticationException($errorMessage);
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param mixed $credentials
     *
     * @param UserProviderInterface $userProvider
     * @return object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy([SecurityController::USERNAME_FIELD => $credentials[SecurityController::USERNAME_FIELD]]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If false is returned, authentication will fail. You may also throw
     * an AuthenticationException if you wish to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param mixed $credentials
     *
     * @param UserInterface $user
     * @return bool
     *
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials[SecurityController::PASSWORD_FIELD]);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param $credentials
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials[SecurityController::PASSWORD_FIELD];
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return  new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
