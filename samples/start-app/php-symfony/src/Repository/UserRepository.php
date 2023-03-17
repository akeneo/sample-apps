<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Exception\UserNotFoundException;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upsert(User $user, bool $flush = false): void
    {
        $user = $this->findOneBy(['id' => $user->getId()]) ?? $user;
        $this->getEntityManager()->persist($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->remove($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUser(): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->orderBy('u.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException) {
            throw new UserNotFoundException(UserNotFoundException::USER_NOT_FOUND);
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
