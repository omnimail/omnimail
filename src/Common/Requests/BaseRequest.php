<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 5/4/17
 * Time: 7:34 AM
 */

namespace Omnimail\Common\Requests;

use Omnimail\Common\Credentials;
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
     * @var Credentials
     */
    protected $credentials;

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
     * BaseRequest constructor.
     *
     * @param array $parameters
     *   Any parameters that match credentials fields or properties on the class
     *   will be retained. Others are discarded.
     */
    public function __construct($parameters)
    {
        $parameters = array_merge($this->getDefaultParameters(), $parameters);
        Helper::initialize($this, $parameters);
    }
}
