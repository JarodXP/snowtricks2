<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Gets the trick list for the admin list table
     * @param array $queryParameters
     * @return Paginator
     */
    public function getPaginatedList(array $queryParameters):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->select('c')
            ->setFirstResult($queryParameters['offset'])
            ->setMaxResults($queryParameters['limit'])
            ->join('c.trick', 't')
            ->join('c.user', 'u')
            ->orderBy('c.dateAdded', 'DESC')
            ->where('t.id = '.$queryParameters['trickId']);

        return new Paginator($queryBuilder->getQuery());
    }
}
