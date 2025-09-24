<?php

namespace Omnimail\Common;

use Exception;

/**
 * Helper class
 * Inspired by / copied from Omnipay library.
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
     * Resolve a short Mailer name to a full namespaced Mailer class.
     * @param  string $className
     * @return string
     * @throws Exception
     */
    public static function getMailerClassName($className)
    {
        if (class_exists($className)) {
            return $className;
        }

        // replace underscores with namespace marker, PSR-0 style
        $fullyQualified = '\\Omnimail\\' . str_replace('_', '\\', $className);
        if (!class_exists($fullyQualified)) {
            $fullyQualified = $fullyQualified . '\\Mailer';
            if (!class_exists($fullyQualified)) {
                throw new Exception("Class '{$className}' not found");
            }
        }

        return $fullyQualified;
    }
}
