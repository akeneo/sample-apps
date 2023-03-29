<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Exception\UserNotFoundException;
use App\Entity\User;
use App\UseCase\Codec;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(
        ManagerRegistry $registry,
        private readonly string $subHashKey)
    {
        parent::__construct($registry, User::class);
    }

    public function upsert(User $user, bool $flush = false): void
    {
        $user = $this->findOneBy(['sub' => $user->getSub()]) ?? $user;
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
    public function getUser(string $sub): ?User
    {
        $user = $this->findOneBy(['sub' => $sub]);

        if (is_null($user)) {
            throw new UserNotFoundException(UserNotFoundException::USER_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserFromCookies(array $cookies): User
    {
        $sub = '';
        $encodedSub = '';
        $iv = '';

        foreach ($cookies as $cookie) {
            switch ($cookie->getName()) {
                case 'sub':
                    $encodedSub = $cookie->getValue();
                    break;
                case 'vector':
                    $iv = $cookie->getValue();
                    break;
                default:
                    break;
            }
        }


        if ($encodedSub != '' && $iv != '') {
            $sub = Codec::decode($encodedSub, $this->subHashKey, $iv);
        }

        return $this->getUser($sub);
    }
}
