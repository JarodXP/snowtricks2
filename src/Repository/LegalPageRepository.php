<?php

declare(strict_types=1);

namespace App\Repository;

use App\CustomServices\AbstractLister;
use App\Entity\LegalPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LegalPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalPage[]    findAll()
 * @method LegalPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalPageRepository extends ServiceEntityRepository
{
    /**
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalPage::class);
    }

    /**
     * Gets the trick list for the admin list table
     * @param array $queryParameters
     * @return Paginator
     */
    public function getAdminLegalList(array $queryParameters):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->select('l')
            ->setFirstResult($queryParameters[AbstractLister::OFFSET_FIELD])
            ->setMaxResults($queryParameters[AbstractLister::LIMIT_FIELD])
            ->orderBy('l.'.$queryParameters[AbstractLister::ORDER_FIELD], $queryParameters[AbstractLister::DIRECTION_FIELD]);

        return new Paginator($queryBuilder->getQuery());
    }
}
