<?php

declare(strict_types=1);

namespace App\Entity\Exception;

class AccessTokenNotFoundException extends \Exception
{
    const ACCESS_TOKEN_NOT_FOUND = 'No access token was found for query although at least one row was expected.';
}
