<?php
/**
 * Base payment gateway class
 */

namespace Omnimail\Common;

use Omnimail\EmailSenderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Base payment Mailer class.
 *
 * Inspired by and copied from Omnipay.
 *
 * This abstract class should be extended by all Mailers
 * throughout the Omnimail system.  It enforces implementation of
 * the GatewayInterface interface and defines various common attibutes
 * and methods that all gateways should have.
 *
 * For further code examples see the *omnipay-example* repository on github.
 *
 * @see EmailInterface
 */
abstract class AbstractMailer implements EmailSenderInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new mailer instance
     */
    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(array $parameters = array())
    {
        $this->parameters = new ParameterBag;

        // set default parameters
        foreach ($this->getDefaultParameters() as $key => $value) {
            if (is_array($value)) {
                $this->parameters->set($key, reset($value));
            } else {
                $this->parameters->set($key, $value);
            }
        }

        Helper::initialize($this, $parameters);

        return $this;
    }

    public function getDefaultParameters()
    {
        return array();
    }

    public function getParameters()
    {
        return $this->parameters->all();
    }

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }
}
