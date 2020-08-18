<?php

namespace Omnimail;

class Email implements EmailInterface
{
    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var array
     */
    protected $replyTo = [];

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var array
     */
    protected $from;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $textBody;

    /**
     * @var string
     */
    protected $htmlBody;

    /**
     * @return string
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * @param string $textBody
     * @return $this
     */
    public function setTextBody($textBody)
    {
        $this->textBody = $textBody;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @param string $htmlBody
     * @return $this
     */
    public function setHtmlBody($htmlBody)
    {
        $this->htmlBody = $htmlBody;
        return $this;
    }

    /**
     * @return array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setFrom($email, $name = null)
    {
        $this->from = ['email' => $email, 'name' => $name];
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    public function getTos()
    {
        return $this->to;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addTo($email, $name = null)
    {
        $this->to[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getCcs()
    {
        return $this->cc;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addCc($email, $name = null)
    {
        $this->cc[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getBccs()
    {
        return $this->bcc;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addBcc($email, $name = null)
    {
        $this->bcc[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getReplyTos()
    {
        return $this->replyTo;
    }

    /**
     * @return array
     */
    public function getReplyTo()
    {
        return count($this->replyTo) > 0 ? $this->replyTo[0] : [];
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function addReplyTo($email, $name = null)
    {
        $this->replyTo[] = [
            'email' => $email,
            'name' => $name
        ];
        return $this;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplyTo($email, $name = null)
    {
        $this->replyTo = [[
            'email' => $email,
            'name' => $name
        ]];
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param AttachmentInterface $attachment
     * @return $this
     */
    public function addAttachment(AttachmentInterface $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $attachments = $this->getAttachments();
        if (count($attachments)) {
            /**
             * @var int $key
             * @var AttachmentInterface $attachment
             */
            foreach ($attachments as $key => $attachment) {
                $attachments[$key] = $attachment->toArray();
            }
        }

        return [
            'textBody' => $this->getTextBody(),
            'htmlBody' => $this->getHtmlBody(),
            'from' => $this->getFrom(),
            'subject' => $this->getSubject(),
            'attachments' => $attachments,
            'tos' => $this->getTos(),
            'replyTos' => $this->getReplyTos(),
            'ccs' => $this->getCcs(),
            'bccs' => $this->getBccs()
        ];
    }
}
