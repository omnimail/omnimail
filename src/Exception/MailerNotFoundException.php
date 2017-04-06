<?php

namespace Omnimail\Exception;

class MailerNotFoundException extends Exception
{
    public function __construct($mailer = null, $code = 603, \Exception $previous = null)
    {
        $message = sprintf('Mailer %s Not Found', $mailer);
        parent::__construct($message, $code, $previous);
    }
}
