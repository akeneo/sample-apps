<?php

namespace App\Entity\Exception;

class AccessTokenNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No access token was found for query although at least one row was expected.');
    }
}