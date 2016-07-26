<?php

namespace Omnimail\Tests\Mock;

use Omnimail\AmazonSES as Base;
use Psr\Log\LoggerInterface;
use GuzzleHttp\HandlerStack;

class AmazonSES extends Base
{
    /**
     * @param string $accessKey
     * @param string $secretKey
     * @param string $host
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        $accessKey,
        $secretKey,
        $host = self::AWS_US_EAST_1,
        LoggerInterface $logger = null,
        HandlerStack $handler = null
    ) {
        parent::__construct($accessKey, $secretKey, $host, $logger);
    }
}
