<?php

namespace App\EventListener;

use App\Exception\RedirectException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Listen the kernel.exception event in order to redirect to an url
 */
class RedirectExceptionListener
{
    /**
     * Return a RedirectResponse if a RedirectException is thrown
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        //Checks if the event is RedirectException
        if(($exception = $event->getThrowable()) instanceof RedirectException){
            $event->setResponse($exception->getResponse());
        }
    }

}