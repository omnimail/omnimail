<?php
/**
 * Helper class
 *
 * Inspired by / copied from Omnipay library.
 */

namespace Omnimail\Common;

/**
 * Helper class
 *
 * This class defines various static utility functions that are in use
 * throughout the Omnimail system.
 */
class Helper
{
    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed.
     *
     * @param  string  $str The input string
     * @return string camelCased output string
     */
    public static function camelCase($str)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * Parameters are automatically converted to camelCase. Any parameters which do
     * not match a setter on the target object are ignored.
     *
     * @param mixed $target     The object to set parameters on
     * @param array $parameters An array of parameters to set
     */
    public static function initialize($target, $parameters)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $method = 'set'.ucfirst(static::camelCase($key));
                if (method_exists($target, $method)) {
                    $target->$method($value);
                }
            }
        }
    }

    /**
     * Resolve a Mailer class to a short name.
     *
     * The short name can be used with Factory as an alias of the mailer class,
     * to create new instances of a mailer.
     */
    public static function getMailerShortName($className)
    {
        if (0 === strpos($className, '\\')) {
            $className = substr($className, 1);
        }

        if (0 === strpos($className, 'Omnimail\\')) {
            return trim(str_replace('\\', '_', substr($className, 8, -7)), '_');
        }

        return '\\'.$className;
    }

  /**
   * Resolve a short Mailer name to a full namespaced Mailer class.
   *
   * Class names beginning with a namespace marker (\) are left intact.
   * Non-namespaced classes are expected to be in the \Omnipay namespace, e.g.:
   *
   *      \Custom\Mailer     => \Custom\Mailer
   *      \Custom_Mailer     => \Custom_Mailer
   *      Stripe              => \Omnipay\Stripe\Mailer
   *      PayPal\Express      => \Omnipay\PayPal\ExpressMailer
   *      PayPal_Express      => \Omnipay\PayPal\ExpressMailer
   *
   * @param  string $shortName The short Mailer name
   * @return string The fully namespaced Mailer class name
   *
   * @throws \Exception
   */
    public static function getMailerClassName($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = '\\Omnimail\\' . str_replace('_', '\\', $shortName);
        if (!class_exists($shortName)) {
            $shortName = $shortName .= '\\Mailer';
            if (!class_exists($shortName)) {
                throw new \Exception("Class '$shortName' not found");
            }
        }

        return $shortName;
    }
}
