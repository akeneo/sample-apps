<?php

namespace App\Client;

use App\Storage\TokenStorageInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

final class ClientFactory
{
    public function __construct(private readonly string $pimUrl, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function create(): ClientInterface
    {
        if($this->pimUrl) {
            return new Client([
                'base_uri' => $this->pimUrl,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->tokenStorage->get()
                ]
            ]);
        }
        return new Client();
    }
}
