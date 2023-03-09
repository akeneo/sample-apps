<?php

declare(strict_types=1);

namespace App\Tests\Mocks;

use App\UseCase\AppActivationCallback;

final class Oauth2Mock
{
    const API_URL = AppActivationCallback::GET_APP_TOKEN_URL;

    public static array $response = [
        "access_token" => "abcdefgh",
        "token_type" => "bearer",
        "scope" => "read_catalog_structure read_catalogs",
    ];
}
