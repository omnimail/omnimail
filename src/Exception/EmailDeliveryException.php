<?php

namespace Omnimail\Exception;

class EmailDeliveryException extends Exception
{
    public function __construct($message = 'Email delivery failed', $code = 600, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
