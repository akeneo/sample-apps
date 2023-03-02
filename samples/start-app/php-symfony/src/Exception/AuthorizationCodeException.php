<?php

namespace App\Exception;

class AuthorizationCodeException extends \Exception
{
    const MISSING_AUTH_CODE = 'Missing authorization code';
}
