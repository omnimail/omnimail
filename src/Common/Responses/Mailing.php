<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 5/4/17
 * Time: 7:34 AM
 */

namespace Omnimail\Common\Responses;

use Omnimail\Exception\InvalidRequestException;

class Mailing
{
    protected $name;

    protected $mailingIdentifier;

    protected $subject;

    protected $htmlBody;

    protected $textBody;

    protected $scheduledTimestamp;

    protected $sendStartTimestamp;

    protected $numberSent;

    protected $numberOpens;

    protected $numberUniqueOpens;

    protected $numberSuppressedByProvider;

    protected $numberBlocked;

    protected $numberBounces;

    protected $numberAbuseReports;

    protected $numberUnsubscribes;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getName()
    {
        return (string)$this->name;
    }

    public function getMailingIdentifier()
    {
        return (string)$this->mailingIdentifier;
    }

    public function getSubject()
    {
        return (string)$this->subject;
    }

    public function getScheduledTimestamp()
    {
        return $this->scheduledTimestamp;
    }

    public function getSendStartTimestamp()
    {
        return $this->sendStartTimestamp;
    }

    public function getHtmlBody()
    {
        return (string)$this->htmlBody;
    }

    public function getTextBody()
    {
        return (string)$this->textBody();
    }

    public function getNumberSent()
    {
        return $this->numberSent;
    }

    /**
     * Get the number of times emails from this mailing have been opened.
     *
     * An individual opening the email 5 times would count as 5 opens.
     *
     * @return int
     */
    public function getNumberOpens()
    {
        return $this->numberOpens;
    }

    /**
     * Get the number of unique times emails from this mailing have been opened.
     *
     * An individual opening the email 5 times would count as 1 open.
     *
     * @return int
     */
    public function getNumberUniqueOpens()
    {
        return $this->getNumberUniqueOpens();
    }

    /**
     * Get the number of unsubscribes received from the mailing.
     *
     * @return int
     */
    public function getNumberUnsubscribes()
    {
        return $this->numberUnsubscribes;
    }

    /**
     * Get the number of abuse reports made about the mailing.
     *
     * Most commonly abuse reports include marking an email as spam.
     *
     * @return int
     */
    public function getNumberAbuseReports()
    {
        return $this->numberAbuseReports;
    }

    /**
     * Get the number of bounces from the email.
     *
     * @return int
     */
    public function getNumberBounces()
    {
        return $this->numberBounces;
    }

    /**
     * Get the number of emails suppressed by the provider.
     *
     * Mailing providers may contain their own lists of contacts to not sent mail to.
     * This number reflects the number of emails not sent due to the provider
     * suppressing them.
     *
     * @return int
     */
    public function getNumberSuppressedByProvider()
    {
        return $this->numberSuppressedByProvider;
    }

    /**
     * Get the number of emails blocked by the recipient's provider.
     *
     * Providers such as AOL, gmail may block some or all of the emails
     * based on whitelisting and blacklisting. This returns that number.
     *
     * @return int
     */
    public function getNumberBlocked()
    {
        return $this->numberBlocked;
    }
}
