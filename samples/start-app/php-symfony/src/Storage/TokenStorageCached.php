<?php

declare(strict_types=1);

namespace App\Storage;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TokenStorageCached implements TokenStorageInterface
{
    public function __construct(private CacheInterface $cache)
    {
    }

    public function store(string $token): void
    {
        $this->cache->delete('token');
        $this->cache->get('token', function (ItemInterface $item) use ($token) {
            $item->expiresAfter(null);

            return $token;
        });
    }

    public function get(): ?string
    {
        return $this->cache->get('token', function (ItemInterface $item) {
            return $item->get();
        });
    }
}
