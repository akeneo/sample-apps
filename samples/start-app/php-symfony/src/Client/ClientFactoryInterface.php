<?php

namespace App\Client;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    public function create(): ClientInterface;
}
