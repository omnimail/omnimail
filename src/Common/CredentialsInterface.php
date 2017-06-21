<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 5/4/17
 * Time: 10:35 AM
 */

namespace Omnimail\Common;

/**
 * Interface CredentialsInterface
 *
 * @package Omnimail
 */
interface CredentialsInterface
{
    /**
     * Set credentials.
     *
     * @param array $credentials
     */
    public function setCredentials($credentials);

    /**
     * @param $parameter
     * @return mixed
     */
    public function get($parameter);
}
