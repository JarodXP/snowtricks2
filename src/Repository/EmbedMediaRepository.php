<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmbedMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmbedMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmbedMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmbedMedia[]    findAll()
 * @method EmbedMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmbedMediaRepository extends ServiceEntityRepository
{
    /**
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmbedMedia::class);
    }

    // /**
    //  * @return EmbedMedia[] Returns an array of EmbedMedia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmbedMedia
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
