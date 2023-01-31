<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Exception\AccessTokenNotFoundException;
use App\Entity\Token;

interface TokenRepositoryInterface
{
    public function upsert(Token $token, bool $flush = false): void;

    public function remove(Token $token, bool $flush = false): void;

    /**
     * @throws AccessTokenNotFoundException
     */
    public function getToken(): ?Token;

    public function hasToken(): bool;
}
