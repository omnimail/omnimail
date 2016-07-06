<?php

namespace Omnimail\Exception;

class InvalidRequestException extends Exception
{
    public function __construct($message = 'Invalid request', $code = 601)
    {
        parent::__construct($message, $code);
    }
}
