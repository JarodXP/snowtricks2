<?php

declare(strict_types=1);


namespace App\CustomServices;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractLister
 * @package App\CustomServices
 */
abstract class AbstractLister
{
    public const ORDER_FIELD = 'order';
    public const DIRECTION_FIELD = 'direction';
    public const LIMIT_FIELD = 'limit';
    public const OFFSET_FIELD = 'offset';
    public const FILTER_FIELD = 'filter';

    public const ADMIN_TRICK_LIST = 'getAdminTrickList';
    public const ADMIN_USER_LIST = 'getAdminUserList';
    public const ADMIN_LEGAL_LIST = 'getAdminLegalList';
    public const ADMIN_COMMENT_LIST = 'getAdminCommentList';


    protected EntityManagerInterface $manager;

    protected array $queryParameters;
    protected ?int $page = null;
    protected string $className;

    /**
     * AbstractLister constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Sets the default parameters
     * @return void
     */
    abstract protected function setQueryDefaultParameters():void ;

    /**
     * Sets the parameters from the form
     * @param FormInterface $paginationForm
     * @param int $page
     * @param string $formName
     * @return void
     */
    abstract protected function setQueryParametersFromForm(FormInterface $paginationForm, int $page, string $formName): void ;

    /**
     * Handles the request to set the new query parameters from the submitted form
     * @param Request $request
     * @param string $className
     * @param FormInterface $paginationForm
     * @param int|null $page
     * @return void
     */
    public function setQueryParameters(Request $request, string $className, FormInterface $paginationForm, int $page = null):void
    {
        //Sets the class name to handle the default parameters
        $this->className = $className;

        //Sets the default parameters
        $this->setQueryDefaultParameters();

        $paginationForm->handleRequest($request);

        //Checks form validity and sets the new query parameters
        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {
            $this->setQueryParametersFromForm($paginationForm, $page, $paginationForm->getName());
        }
    }

    /**
     * Gets the queryParameters
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * Calls the corresponding method in the respective repository
     * @param string $className
     * @param string $repoList
     * @return Paginator
     */
    public function getList(string $repoList):Paginator
    {
        return $this->manager
            ->getRepository($this->className)
            ->$repoList($this->queryParameters);
    }

    /**
     * Counts the number of pages depending on the limit
     * @param Paginator $list
     * @return int
     */
    public function getTotalPages(Paginator $list):int
    {
        return (int) round(count($list) / (int) $this->queryParameters[self::LIMIT_FIELD]);
    }
}
