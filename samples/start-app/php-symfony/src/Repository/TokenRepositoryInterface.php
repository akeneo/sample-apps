<?php

namespace App\Repository;

use App\Entity\Exception\NoAccessTokenException;
use App\Entity\Token;

interface TokenRepositoryInterface
{
    public function upsert(Token $token, bool $flush = false): void;

    public function remove(Token $token, bool $flush = false): void;

    /**
     * @throws NoAccessTokenException
     */
    public function getToken(): ?Token;

    public function hasToken(): bool;
}
