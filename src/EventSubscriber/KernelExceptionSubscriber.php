<?php

declare(strict_types=1);


namespace App\EventSubscriber;

use App\Exception\RedirectException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class KernelExceptionSubscriber
 * Handles kernel.exception events
 * @package App\EventSubscribers
 */
class KernelExceptionSubscriber implements EventSubscriberInterface
{
    protected UrlGeneratorInterface $router;
    protected SessionInterface $session;

    /**
     * KernelExceptionSubscriber constructor.
     * @param UrlGeneratorInterface $router
     * @param SessionInterface $session
     */
    public function __construct(UrlGeneratorInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'processException'];
    }

    /**
     * Sets the event response depending on the Exception class
     * @param ExceptionEvent $event
     */
    public function processException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $this->session->getFlashBag()->add('error', 'Access denied');
            $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
        } elseif ($exception instanceof RedirectException) {
            $event->setResponse($exception->getResponse());
        } elseif ($exception instanceof NotFoundHttpException) {
            $this->session->getFlashBag()->add('error', 'The requested page doesn\'t exist');
            $event->setResponse(new RedirectResponse($this->router->generate('home')));
        }
    }
}
