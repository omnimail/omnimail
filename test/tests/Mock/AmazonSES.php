<?php

namespace Omnimail\Tests\Mock;

use Omnimail\AmazonSES as Base;
use Psr\Log\LoggerInterface;
use Http\Adapter\Guzzle6\Client as AdapterClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;

class AmazonSES extends Base
{
    /**
     * @param string $accessKey
     * @param string $secretKey
     * @param string $host
     * @param LoggerInterface|null $logger
     */
    public function __construct($accessKey, $secretKey, $host = self::AWS_US_EAST_1, LoggerInterface $logger = null, HandlerStack $handler = null)
    {
        if (is_null($handler)) {
            $client = new AdapterClient();
        } else {
            $client = new AdapterClient(new GuzzleClient(['handler' => $handler]));
        }

        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->host = $host;
        $this->logger = $logger;
    }
}
