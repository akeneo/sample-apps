<?php

namespace App\Entity\Exception;

class NoAccessTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No access token was found for query although at least one row was expected.');
    }
}