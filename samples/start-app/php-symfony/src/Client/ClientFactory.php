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
        if($this->tokenRepository->hasToken()) {
            return new Client([
                'base_uri' => $this->pimUrl,
                'headers' => [
                    'Authorization' =>sprintf("Bearer %s", $this->tokenRepository->getToken()->getAccessToken()),
                    'X-APP-SOURCE' => 'startApp-symfony',
                ]
            ]);
        }
        return new Client();
    }
}
