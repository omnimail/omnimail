<?php


namespace Omnimail;

class Gmail extends SMTP
{
    public function __construct($emailAddress, $password, array $options = [])
    {
        parent::__construct("smtp.gmail.com", $emailAddress, $password, $options);
    }
}
