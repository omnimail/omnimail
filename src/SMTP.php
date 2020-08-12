<?php

namespace Omnimail;

use Omnimail\Exception\Exception as OmniMailException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

class SMTP implements MailerInterface
{
    /**
     * @var PHPMailer
     */
    protected $processor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SMTPMailer constructor.
     * @param $smtpHost
     * @param $username
     * @param $password
     * @param array $options
     */
    public function __construct($smtpHost, $username, $password, array $options = [])
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $smtpHost;
        $mailer->SMTPAuth = true;
        $mailer->Username = $username;
        $mailer->Password = $password;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        if (isset($options['timeout'])) {
            $mailer->Timeout = $options['timeout'];
        }

        $mailer->Port = !isset($options['port']) ? 587 : intval($options['port']);
        $this->processor = $mailer;
        $this->processor->SMTPDebug = 2;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param EmailInterface $email
     * @return bool
     * @throws \Omnimail\Exception\Exception
     * @throws Exception
     */
    public function send(EmailInterface $email)
    {
        foreach ($email->getTos() as $to) {
            $this->processor->addAddress($to['email'], $to['name']);
        }

        if (!empty($email->getReplyTo())) {
            $this->processor->addReplyTo($email->getReplyTo()['email'], $email->getReplyTo()['name']);
        }

        if (!empty($email->getReplyTos())) {
            foreach ($email->getReplyTos() as $replyTo) {
                $this->processor->addReplyTo($replyTo['email'], $replyTo['name']);
            }
        }

        foreach ($email->getCcs() as $cc) {
            $this->processor->addCC($cc['email'], $cc['name']);
        }

        foreach ($email->getBccs() as $bcc) {
            $this->processor->addBCC($bcc['email'], $bcc['name']);
        }

        if (!empty($email->getFrom())) {
            $this->processor->setFrom($email->getFrom()['email'], $email->getFrom()['name']);
        }

        if (!empty($email->getSubject())) {
            $this->processor->Subject = $email->getSubject();
        }

        if (!empty($email->getHtmlBody())) {
            $this->processor->isHTML(true);
            $this->processor->Body = $email->getHtmlBody();
        }

        if (!empty($email->getTextBody()) && !empty($email->getHtmlBody())) {
            $this->processor->AltBody = $email->getTextBody();
        }

        if (!empty($email->getTextBody()) && empty($email->getHtmlBody())) {
            $this->processor->Body = $email->getTextBody();
        }

        if (!empty($email->getAttachments())) {
            foreach ($email->getAttachments() as $attachment) {
                if (!empty($attachment->getPath())) {
                    $this->processor->addAttachment(
                        $attachment->getPath(),
                        $attachment->getName(),
                        PHPMailer::ENCODING_BASE64,
                        $attachment->getMimeType()
                    );
                } elseif (!empty($attachment->getContent())) {
                    $this->processor->addStringAttachment(
                        $attachment->getContent(),
                        $attachment->getName(),
                        PHPMailer::ENCODING_BASE64,
                        $attachment->getMimeType()
                    );
                }
            }
        }

        try {
            $this->processor->send();
            return true;
        } catch (Exception $e) {
            throw new OmniMailException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}
