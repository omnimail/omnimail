<?php
/**
 * Omnimail class
 *
 * This class was inspired by / copied from the Omnipay library.
 */

namespace Omnimail;

use Omnimail\Common\Factory;

/**
 * Omnimail class
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
 *   // Initialise the mailer
 *   $mailer->initialize(...);
 *
 *   // Get the mailer parameters.
 *   $parameters = $mailer->getParameters();
 *
 * </code>
 *
 * For further code examples see the *omnipay-example* repository on github.
 *
 * @method static array  all()
 * @method static array  replace(array $mailers)
 * @method static string register(string $className)
 * @method static array  find()
 * @method static array  getSupportedMailers()
 * @codingStandardsIgnoreStart
 * @method static \Omnipay\Common\MailerInterface create(string $class, Client $httpClient = null, \Symfony\Component\HttpFoundation\Request $httpRequest = null)
 * @codingStandardsIgnoreEnd
 *
 * @see Omnimail\Common\Factory
 */
class Omnimail
{

    /**
     * Internal factory storage
     *
     * @var Factory
     */
    private static $factory;

    /**
     * Get the Mailer factory
     *
     * Creates a new empty Factory if none has been set previously.
     *
     * @return Factory A Factory instance
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
     *
     * @param Factory $factory A Factory instance
     */
    public static function setFactory(Factory $factory = null)
    {
        static::$factory = $factory;
    }

    /**
     * Static function call router.
     *
     * All other function calls to the Omnipay class are routed to the
     * factory.  e.g. Omnipay::getSupportedMailers(1, 2, 3, 4) is routed to the
     * factory's getSupportedMailers method and passed the parameters 1, 2, 3, 4.
     *
     * Example:
     *
     * <code>
     *   // Create a Mailer for the PayPal ExpressGateway
     *   $gateway = Omnipay::create('ExpressGateway');
     * </code>
     *
     * @see Factory
     *
     * @param string $method     The factory method to invoke.
     * @param array  $parameters Parameters passed to the factory method.
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $factory = static::getFactory();

        return call_user_func_array(array($factory, $method), $parameters);
    }
}
