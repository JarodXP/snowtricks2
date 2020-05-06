<?php

declare(strict_types=1);

namespace App\Repository;

use App\CustomServices\AbstractLister;
use App\CustomServices\HomeTrickLister;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * Gets the trick list for Home trick grid
     * @param array $queryParameters
     * @return Paginator
     */
    public function getHomeTrickList(array $queryParameters):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->select('t')
            ->setMaxResults($queryParameters[AbstractLister::LIMIT_FIELD])
            ->join('t.trickGroup', 'tg')
            ->orderBy('tg.name');

        if (isset($queryParameters[HomeTrickLister::FILTER_ID]) && !is_null($queryParameters[HomeTrickLister::FILTER_ID])) {
            $queryBuilder->where('tg.id = '.$queryParameters[HomeTrickLister::FILTER_ID]);
        }

        return new Paginator($queryBuilder->getQuery());
    }

    /**
     * Gets the trick list for the admin list table
     * @param array $queryParameters
     * @return Paginator
     */
    public function getAdminTrickList(array $queryParameters):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->select('t')
            ->setFirstResult($queryParameters[AbstractLister::OFFSET_FIELD])
            ->setMaxResults($queryParameters[AbstractLister::LIMIT_FIELD])
            ->join('t.trickGroup', 'tg')
            ->join('t.author', 'a')
            ->orderBy('t.'.$queryParameters[AbstractLister::ORDER_FIELD], $queryParameters[AbstractLister::DIRECTION_FIELD]);

        return new Paginator($queryBuilder->getQuery());
    }
}
