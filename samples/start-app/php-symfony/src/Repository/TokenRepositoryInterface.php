<?php

namespace App\Repository;

use App\Entity\Exception\NoAccessTokenException;
use App\Entity\Token;

interface TokenRepositoryInterface
{
    public function save(Token $entity, bool $flush = false): void;

    public function remove(Token $entity, bool $flush = false): void;

    /**
     * @throws NoAccessTokenException
     */
    public function getToken(): ?Token;
}
