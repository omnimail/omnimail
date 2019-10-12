<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 5/4/17
 * Time: 7:34 AM
 */

namespace Omnimail\Common\Responses;

use Omnimail\Common\Helper;

abstract class BaseResponse extends \arrayObject
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
     * Get defaults for the api.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array();
    }

    /**
     * Is the data retrieved yet.
     *
     * If the function is asynchronous then there may be a need to check and
     * retry until it is complete.
     */
    public function isCompleted()
    {
        true;
    }
}
