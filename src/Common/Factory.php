<?php
/**
 * Omnimail Factory class
 */

namespace Omnimail\Common;

/**
 * Omnimail Mailer Factory class
 *
 * This class abstracts a set of mailers that can be independently
 * registered, accessed, and used.
 *
 * Note that static calls to the Omnipay class are routed to this class by
 * the static call router (__callStatic) in Omnipay.
 *
 * Example:
 *
 * <code>
 *   // Create a mailer for Mailgun
 *   // (routes to Factory::create)
 *   $mailer = Omnimail::create('EMailgun');
 * </code>
 *
 * @see Omnimail/Omnimail
 */
class Factory
{
    /**
     * Internal storage for all available mailers
     *
     * @var array
     */
    private $mailers = array();

    /**
     * All available mailers
     *
     * @return array An array of mailer names
     */
    public function all()
    {
        return $this->mailers;
    }

    /**
     * Replace the list of available mailers
     *
     * @param array $mailers An array of mailer names
     */
    public function replace(array $mailers)
    {
        $this->mailers = $mailers;
    }

    /**
     * Register a new mailer
     *
     * @param string $className Mailer name
     */
    public function register($className)
    {
        if (!in_array($className, $this->mailers)) {
            $this->mailers[] = $className;
        }
    }

    /**
     * Automatically find and register all officially supported mailers
     *
     * @return array An array of mailer names
     */
    public function find()
    {
        foreach ($this->getSupportedMailers() as $mailer) {
            $class = Helper::getMailerClassName($mailer);
            if (class_exists($class)) {
                $this->register($mailer);
            }
        }

        ksort($this->mailers);

        return $this->all();
    }

    /**
     * Create a new mailer instance
     *
     * @param string               $class       Mailer name
     * @param ClientInterface|null $httpClient  A Guzzle HTTP Client implementation
     * @param HttpRequest|null     $httpRequest A Symfony HTTP Request implementation
     *
     * @throws \Exception                 If no such mailer is found
     * @return object An object of class $class is created and returned
     */
    public function create($class, ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $class = Helper::getMailerClassName($class);

        if (!class_exists($class)) {
            throw new \Exception("Class '$class' not found");
        }

        return new $class($httpClient, $httpRequest);
    }

    /**
     * Get a list of supported mailers which may be available
     *
     * @return array
     */
    public function getSupportedMailers()
    {
        $package = json_decode(file_get_contents(__DIR__.'/../../../composer.json'), true);

        return $package['extra']['mailers'];
    }
}
