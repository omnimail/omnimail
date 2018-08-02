<?php

namespace Omnimail\Common;

use Omnimail\MailerInterface;
use Omnimail\Common\Requests\RequestInterface;
use Omnimail\Common\Credentials;

abstract class AbstractMailer implements MailerInterface
{
    /**
     * Guzzle client, overridable with mock object in tests.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @return \Omnimail\Common\Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param \Omnimail\Common\Credentials $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

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
     * using existing parameters from this gateway.
     *
     * If a client has been set on this class it will passed through,
     * allowing a mock guzzle client to be used for testing.
     *
     * Example:
     *
     * <code>
     *   function myRequest($parameters) {
     *     $this->createRequest('MyRequest', $parameters);
     *   }
     *   class MyRequest extends SilverpopBaseRequest {};
     *
     *   // Create the mailer
     *   $mailer = Omnimail::create('Silverpop', $parameters);
     *
     *   // Create the request object
     *   $myRequest = $mailer->myRequest($someParameters);
     * </code>
     *
     * @param string $class The request class name
     * @param array $parameters
     *
     * @return RequestInterface
     */
    protected function createRequest($class, array $parameters)
    {
        if (!isset($parameters['credentials'])) {
            $parameters['credentials'] = new Credentials(array_intersect_key($this->getCredentialFields(), $parameters));
        }
        return new $class($parameters);
    }

    /**
     * Get an array of the credential fields.
     *
     * @return array
     *   Array keyed by fieldname with details as fields - eg.
     *    array(
     *      'username' => array('type' => 'String', 'required' => TRUE),
     *       'password' => array('type' => 'String', 'required' => TRUE),
     *       'engage_server' => array('type' => 'String', 'required' => FALSE, 'default' => 4),
     *   );
     */
    public function getCredentialFields()
    {
        return array();
    }
}
