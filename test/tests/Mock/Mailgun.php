<?php

namespace Omnimail\Tests\Mock;

use Omnimail\Mailgun as Base;
use Http\Adapter\Guzzle6\Client as AdapterClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Mailgun\Mailgun as MailgunAPI;

class Mailgun extends Base
{
    /**
     * @param string $apiKey
     * @param string $domain
     * @param LoggerInterface|null $logger
     * @param MockHandler|null $logger
     */
    public function __construct($apiKey, $domain, LoggerInterface $logger = null, HandlerStack $handler = null)
    {
        if (is_null($handler)) {
            $client = new AdapterClient();
        } else {
            $client = new AdapterClient(new GuzzleClient(['handler' => $handler]));
        }

        $this->apiKey = $apiKey;
        $this->domain = $domain;
        $this->logger = $logger;
        $this->mailgun = new MailgunAPI($this->apiKey, $client);
    }
}