<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 5/4/17
 * Time: 7:34 AM
 */

namespace Omnimail\Common\Requests;

use Omnimail\Common\Helper;

abstract class BaseRequest implements RequestInterface
{

    /**
     * Timestamp for start of period.
     *
     * @var int
     */
    protected $startTimeStamp;

    /**
     * Timestamp for end of period.
     *
     * @var int
     */
    protected $endTimeStamp;

    /**
     * Url to direct requests to.
     *
     * @var string
     */
    protected $endPoint;

    /**
     * User name
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return int
     */
    public function getStartTimeStamp()
    {
        return $this->startTimeStamp;
    }

    /**
     * @param int $startTimeStamp
     */
    public function setStartTimeStamp($startTimeStamp)
    {
        $this->startTimeStamp = $startTimeStamp;
    }

    /**
     * @return int
     */
    public function getEndTimeStamp()
    {
        return $this->endTimeStamp;
    }

    /**
     * @param int $endTimeStamp
     */
    public function setEndTimeStamp($endTimeStamp)
    {
        $this->endTimeStamp = $endTimeStamp;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUsername($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function __construct($parameters)
    {
        Helper::initialize($this, array_merge($this->getDefaultParameters(), $parameters));
    }
}
