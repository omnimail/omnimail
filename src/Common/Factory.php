<?php

namespace Omnimail\Common;

use Omnimail\MailerInterface;
use Omnimail\Exception\MailerNotFoundException;

/**
 * Omnimail Mailer Factory class
 *
 * This class abstracts a set of mailers that can be independently
 * registered, accessed, and used.
 *
 * Example:
 *
 * <code>
 *   // Create a mailer for Mailgun
 *   // (routes to Factory::create)
 *   $mailer = Omnimail::create('EMailgun');
 * </code>
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
     * @param string $class
     * @param array $parameters
     * @throws MailerNotFoundException
     * @return MailerInterface
     */
    public function create($class, array $parameters = [])
    {
        $class = Helper::getMailerClassName($class);

        if (!class_exists($class)) {
            throw new MailerNotFoundException($class);
        }

        $instance = new $class();
        if (method_exists($instance, 'getCredentialFields') && !isset($parameters['credentials'])) {
            $parameters['credentials'] = new Credentials(array_intersect_key($parameters, $instance->getCredentialFields()));
        }
        Helper::initialize($instance, $parameters);
        return $instance;
    }

    /**
     * Get a list of supported mailers which may be available
     * @return array
     */
    public function getSupportedMailers()
    {
        // todo: this would require to read a file and should be considered too slow, it would be
        // better to detect the available classes instead (class_exists('...'))
        //
        //$package = json_decode(file_get_contents(__DIR__.'/../../../composer.json'), true);
        //return $package['extra']['mailers'];
    }
}
