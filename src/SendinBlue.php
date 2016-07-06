<?php

namespace Omnimail;

use Psr\Log\LoggerInterface;

class SendinBlue implements EmailSenderInterface
{
    private $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {
    }
}
