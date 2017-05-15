<?php

namespace Omnimail;

use Omnimail\Common\Factory;

/**
 * Omnimail class
 * This class was inspired by / copied from the Omnipay library.
 *
 * Provides static access to the gateway factory methods.  This is the
 * recommended route for creation and establishment of mail gateway
 * objects via the standard Factory.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Silverpop mail client
 *   // (routes to Factory::create)
 *   $mailer= Omnimail::create('Silverpop');
 *
 *   // Get the mailer parameters.
 *   $parameters = $mailer->getParameters();
 * </code>
 *
 * @method static array all()
 * @method static array replace(array $mailers)
 * @method static string register(string $className)
 * @method static array find()
 * @method static array getSupportedMailers()
 * @method static MailerInterface create(string $class, array $parameters = [])
 */
class Omnimail
{
    const AMAZONSES = '\Omnimail\AmazonSES';
    const MAILGUN = '\Omnimail\Mailgun';
    const MAILJET = '\Omnimail\Mailjet';
    const MANDRILL = '\Omnimail\Mandrill';
    const POSTMARK = '\Omnimail\Postmark';
    const SENDGRID = '\Omnimail\Sendgrid';
    const SENDINBLUE = '\Omnimail\SendinBlue';

    private static $factory;

    /**
     * Get the Mailer factory
     * Creates a new empty Factory if none has been set previously.
     * @return Factory
     */
    public static function getFactory()
    {
        if (is_null(static::$factory)) {
            static::$factory = new Factory;
        }
        return static::$factory;
    }

    /**
     * Set the Mailer factory
     * @param Factory $factory A Factory instance
     */
    public static function setFactory(Factory $factory = null)
    {
        static::$factory = $factory;
    }

    /**
     * Static function call router.
     * @param string $method     The factory method to invoke.
     * @param array  $parameters Parameters passed to the factory method.
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $factory = static::getFactory();
        return call_user_func_array(array($factory, $method), $parameters);
    }
}
