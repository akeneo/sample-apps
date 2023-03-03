<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Exception\AccessTokenNotFoundException;
use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Token>
 *
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository implements TokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function upsert(Token $token, bool $flush = false): void
    {
        $token = $this->findOneBy(['accessToken' => $token->getAccessToken()]) ?? $token;
        $this->getEntityManager()->persist($token);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Token $token, bool $flush = false): void
    {
        $this->getEntityManager()->remove($token);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws AccessTokenNotFoundException
     */
    public function getToken(): ?Token
    {
        try {
            return $this->createQueryBuilder('t')
                ->orderBy('t.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException) {
            throw new AccessTokenNotFoundException(AccessTokenNotFoundException::ACCESS_TOKEN_NOT_FOUND);
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function hasToken(): bool
    {
        return (bool) $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
