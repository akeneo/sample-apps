<?php

namespace App\Entity\Exception;

class OpenIdConnectException extends \Exception
{
    const MISSING_CLAIM = 'One or several user profile claims are missing';
    const NO_PUBLIC_KEY = 'Failed to retrieve openid public key';
    const PUBLIC_KEY_NOT_STRING = 'OpenID public key is not a string';
}
