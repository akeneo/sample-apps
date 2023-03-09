<?php

namespace App\Entity\Exception;

class SessionInformationException extends \Exception
{
    const MISSING_TOKEN = 'No token in session';
    const MISSING_PIM_URL = 'No PIM url in session';

}
