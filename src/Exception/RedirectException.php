<?php

declare(strict_types=1);


namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class RedirectException
 * Creates a specific exception in order to execute redirections from a service
 * @package App\Exception
 */
class RedirectException extends Exception
{
    /**
     * @var string target url where to redirect the user
     */
    private string $url;
    /**
     * @var int Http code of the redirection (301, 302,..)
     */
    private int $codeHttp;

    /**
     * RedirectException constructor ...
     * @param string $url
     * @param int $codeHttp
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $url,
        int $codeHttp = Response::HTTP_MOVED_PERMANENTLY,
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->url = $url;
        $this->codeHttp = $codeHttp;
    }

    /**
     * Returns a RedirectResponse to the given URL ...
     */
    public function getResponse(): Response
    {
        return new RedirectResponse($this->url, $this->codeHttp);
    }
}
