<?php

namespace Omnimail\Exception;

class InvalidRequestException extends Exception
{
    public function __construct($message = 'Invalid request', $code = 601, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
