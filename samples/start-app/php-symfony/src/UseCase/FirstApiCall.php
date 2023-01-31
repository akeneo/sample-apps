<?php

namespace App\UseCase;

use App\Client\ClientFactory;

class FirstApiCall
{
    public function __construct(private readonly ClientFactory $clientFactory)
    {
    }

    public function execute(): array
    {
        // Create instance of client with 'base_uri' configured in .env file and access token stored in database
        $client =  $this->clientFactory->create();

        // Replace by the API endpoint you want to call. Here an example with channels
        // https://api.akeneo.com/api-reference.html#get_channels
        $apiUrl = '/api/rest/v1/channels';

        $response =  $client->get($apiUrl);

        return json_decode($response->getBody()->getContents(), true);
    }
}
