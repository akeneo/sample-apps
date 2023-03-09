<?php

namespace App\Entity\Exception;

class QueryParametersException extends \Exception
{
    const MISSING_PIM_URL = 'Missing PIM URL in the query';
}
