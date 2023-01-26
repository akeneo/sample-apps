<?php

namespace App\Client;

use App\Repository\TokenRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

final class ClientFactory
{
    public function __construct(private readonly string $pimUrl, private readonly TokenRepository $tokenRepository)
    {
    }

    public function create(): ClientInterface
    {
        $token = $this->tokenRepository->getToken();
        if($token) {
            return new Client([
                'base_uri' => $this->pimUrl,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token->getAccessToken()
                ]
            ]);
        }
        return new Client();
    }
}
