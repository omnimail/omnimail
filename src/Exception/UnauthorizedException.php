<?php

namespace Omnimail\Exception;

class UnauthorizedException extends Exception
{
    public function __construct($message = 'Unauthorized', $code = 602, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
