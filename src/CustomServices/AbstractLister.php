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
     * @return void
     */
    abstract protected function queryDefaultParameters():void ;

    /**
     * @param FormInterface $paginationForm
     * @param int $page
     * @return void
     */
    abstract protected function getQueryParametersFromForm(FormInterface $paginationForm, int $page): void ;

    /**
     * @param Request $request
     * @param FormInterface $paginationForm
     * @param int|null $page
     * @return array
     */
    public function getQueryParameters(Request $request, string $className, FormInterface $paginationForm, int $page = null):array
    {
        $this->className = $className;

        $this->queryDefaultParameters();

        $paginationForm->handleRequest($request);

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {
            $this->getQueryParametersFromForm($paginationForm, $page);
        }

        return $this->queryParameters;
    }

    /**
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
     * @param Paginator $list
     * @return int
     */
    public function getTotalPages(Paginator $list):int
    {
        return (int) round(count($list) / (int) $this->queryParameters[self::LIMIT_FIELD]);
    }
}
