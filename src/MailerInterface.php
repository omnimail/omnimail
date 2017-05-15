<?php

namespace Omnimail;

use Psr\Log\LoggerInterface;

interface MailerInterface
{
    public function send(EmailInterface $email);
}
