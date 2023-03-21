<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Exception\UserNotFoundException;
use App\Entity\User;

interface UserRepositoryInterface
{
    public function upsert(User $user, bool $flush = false): void;

    public function remove(User $user, bool $flush = false): void;

    /**
     * @throws UserNotFoundException
     */
    public function getUser(string $sub): ?User;

    /**
     * @throws UserNotFoundException
     */
    public function getUserFromCookies(array $cookies): ?User;
}
