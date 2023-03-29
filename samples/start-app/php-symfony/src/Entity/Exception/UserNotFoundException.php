<?php

declare(strict_types=1);

namespace App\Entity\Exception;

class UserNotFoundException extends \Exception
{
    const USER_NOT_FOUND = 'User not found';
}
