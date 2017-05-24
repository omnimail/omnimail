<?php

namespace Omnimail\Common;

use Omnimail\MailerInterface;
use Omnimail\Common\Requests\RequestInterface;

abstract class AbstractMailer implements MailerInterface
{
    /**
     * Guzzle client, overridable with mock object in tests.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Initialize a request object
     *
     * This function is usually used to initialise objects of type
     * BaseRequest (or a non-abstract subclass of it)
     * with using existing parameters from this gateway.
     *
     * The request object is passed in, allowing for a non-interactive instance
     * to be used in developer mode.
     *
     * Example:
     *
     * <code>
     *   class MyRequest extends \Omnipay\Common\Message\AbstractRequest {};
     *
     *   class MyGateway extends \Omnipay\Common\AbstractGateway {
     *     function myRequest($parameters) {
     *       $this->createRequest('MyRequest', $request, $parameters);
     *     }
     *   }
     *
     *   // Create the gateway object
     *   $gw = Omnimail::create('MyGateway');
     *
     *   // Create the request object
     *   $myRequest = $gw->myRequest($someParameters);
     * </code>
     *
     * @param string $class The request class name
     * @param RequestInterface $request
     * @param array $parameters
     *
     * @return RequestInterface
     */
    protected function createRequest($class, RequestInterface $request, array $parameters)
    {
        return new $class($parameters);
    }
}
