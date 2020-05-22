<?php

declare(strict_types=1);

namespace App\Repository;

use App\CustomServices\AbstractLister;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Gets the trick list for the admin list table
     * @param array $queryParameters
     * @return Paginator
     */
    public function getAdminUserList(array $queryParameters):Paginator
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder
            ->select('u')
            ->setFirstResult($queryParameters[AbstractLister::OFFSET_FIELD])
            ->setMaxResults($queryParameters[AbstractLister::LIMIT_FIELD])
            ->join('u.avatar', 'a')
            ->orderBy('u.'.$queryParameters[AbstractLister::ORDER_FIELD], $queryParameters[AbstractLister::DIRECTION_FIELD]);

        return new Paginator($queryBuilder->getQuery());
    }

    /**
     * @param User $user
     * @throws ORMException
     */
    public function remove(User $user)
    {
        $manager = $this->getEntityManager();

        //Gets the anonymous user
        $anonymous = $this->findOneBy(['username' => 'Anonymous']);

        //Gets all the tricks from the user to be removed
        $tricks = $user->getTricks();

        //Transfers the tricks to an anonymous user
        foreach ($tricks as $trick) {
            $trick->setAuthor($anonymous);
            $manager->persist($trick);
        }

        //Removes the user
        $manager->remove($user);
    }
}
