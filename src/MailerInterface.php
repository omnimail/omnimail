<?php

namespace Omnimail;

interface MailerInterface
{
    public function send(EmailInterface $email);
}
