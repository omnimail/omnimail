<?php

namespace Omnimail;

use Psr\Log\LoggerInterface;

interface EmailSenderInterface
{
    public function send(EmailInterface $email);
}
