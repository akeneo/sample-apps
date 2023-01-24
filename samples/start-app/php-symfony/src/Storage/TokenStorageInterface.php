<?php

declare(strict_types=1);

namespace App\Storage;

interface TokenStorageInterface
{
    public function store(string $token): void;

    public function get(): ?string;
}
