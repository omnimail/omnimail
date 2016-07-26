<?php

namespace Omnimail;

interface EmailInterface
{
    /**
     * @return string
     */
    public function getTextBody();

    /**
     * @param string $textBody
     * @return $this
     */
    public function setTextBody($textBody);

    /**
     * @return string
     */
    public function getHtmlBody();

    /**
     * @param string $htmlBody
     * @return $this
     */
    public function setHtmlBody($htmlBody);

    /**
     * @return array
     */
    public function getFrom();

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setFrom($email, $name = null);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @return AttachmentInterface[]
     */
    public function getAttachments();

    /**
     * @param AttachmentInterface $attachment
     * @return $this
     */
    public function addAttachment(AttachmentInterface $attachment);

    /**
     * @return array
     */
    public function getTos();

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addTo($email, $name = null);

    /**
     * @return array
     */
    public function getReplyTo();

    /**
     * @return array
     */
    public function getReplyTos();

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addReplyTo($email, $name = null);

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplyTo($email, $name = null);

    /**
     * @return array
     */
    public function getCcs();

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addCc($email, $name = null);

    /**
     * @return array
     */
    public function getBccs();

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addBcc($email, $name = null);

    /**
     * @return array
     */
    public function toArray();
}
