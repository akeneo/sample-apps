<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Client\ClientFactoryInterface;
use GuzzleHttp\Psr7\Response;

final class NotifyAuthorizationUpdate
{
    const GET_APP_SCOPES_UPDATE = '/connect/apps/v1/scopes/update?scopes=';

    public function __construct(private readonly ClientFactoryInterface $clientFactory)
    {
    }

    public function execute(array $oauthScopes): Response
    {
        // Create instance of client with 'base_uri' configured in .env file and access token stored in database
        $client = $this->clientFactory->create();

        $apiUrl = self::GET_APP_SCOPES_UPDATE . implode(' ', $oauthScopes);

        return $client->post($apiUrl, []);
    }
}
