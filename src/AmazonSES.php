<?php

namespace Omnimail;

use Psr\Log\LoggerInterface;

class AmazonSES implements EmailSenderInterface
{
    private $accessKey;
    private $secretKey;
    private $logger;

    /**
     * @param string $accessKey
     * @param string $secretKey
     * @param LoggerInterface|null $logger
     */
    public function __construct($accessKey, $secretKey, LoggerInterface $logger = null)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {

    }
}
