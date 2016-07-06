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
    protected $attachements = [];

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
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
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addTo($email, $name)
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
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addCc($email, $name)
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
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function addBcc($email, $name)
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
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * @param string $name
     * @param string $content
     * @param string $mimeType
     * @return $this
     */
    public function addAttachement($name, $content, $mimeType)
    {
        $this->attachements[] = [
            'name' => $name,
            'content' => $content,
            'mimeType' => $mimeType
        ];
        return $this;
    }

    /**
     * @return string
     */
    public function getToEmail()
    {
        if (isset($this->to[0]['email'])) {
            return $this->to[0]['email'];
        }
        return null;
    }

    /**
     * @param string $toEmail
     * @return $this
     */
    public function setToEmail($toEmail)
    {
        $this->to = array_slice($this->to, 0, 1);
        if (!isset($this->to[0])) {
            $this->to[0] = [];
        }
        $this->to[0]['email'] = $toEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getToName()
    {
        if (isset($this->to[0]['name'])) {
            return $this->to[0]['name'];
        }
        return null;
    }

    /**
     * @param string $toName
     * @return $this
     */
    public function setToName($toName)
    {
        $this->to = array_slice($this->to, 0, 1);
        if (!isset($this->to[0])) {
            $this->to[0] = [];
        }
        $this->to[0]['name'] = $toName;
        return $this;
    }
}
