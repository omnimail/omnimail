<?php

namespace Omnimail\Common;

use Omnimail\MailerInterface;
use Omnimail\Common\Requests\RequestInterface;

abstract class AbstractMailer implements MailerInterface
{
    /**
     * Are we in developer mode.
     *
     * If so interactions will not be sent to the external provider.
     *
     * Some providers may also support test mode, where a test instance
     * of the external api is used.
     *
     * @var bool
     */
    protected $developerMode;

    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->developerMode;
    }

    /**
     * @param bool $developerMode
     */
    public function setDeveloperMode($developerMode)
    {
        $this->developerMode = $developerMode;
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
