<?php

declare(strict_types=1);

namespace App\Repository;

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
     * @param int $limit
     * @param int|null $filterGroupId
     * @return Paginator
     */
    public function getHomeTrickList(int $limit, int $filterGroupId = null):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->select('t')
            ->setMaxResults($limit)
            ->join('t.trickGroup', 'tg')
            ->orderBy('tg.name');

        if (!is_null($filterGroupId)) {
            $queryBuilder->where('tg.id = '.$filterGroupId);
        }

        return new Paginator($queryBuilder->getQuery());
    }
}
