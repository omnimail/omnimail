<?php

namespace Omnimail;

use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use SendGrid\Email as SendGridEmail;
use SendGrid\Content;
use SendGrid\Mail;
use SendGrid\Attachment as SendGridAttachment;
use SendGrid\Personalization;
use SendGrid\Response;

class Sendgrid implements MailerInterface
{
    private $apiKey;
    private $logger;

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $apiKey
     * @param LoggerInterface|null $logger
     */
    public function __construct($apiKey = null, LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {
        $content = null;
        if ($email->getHtmlBody()) {
            $content = new Content("text/html", $email->getHtmlBody());
        } elseif ($email->getTextBody()) {
            $content = new Content("text/plain", $email->getTextBody());
        }

        $from = $this->mapEmail($email->getFrom());

        $personalization = new Personalization();

        $firstTo = true;
        $to = null;
        foreach ($email->getTos() as $recipient) {
            if ($firstTo) {
                $to = $this->mapEmail($recipient);
            } else {
                $personalization->addTo($this->mapEmail($recipient));
            }
        }

        $mail = new Mail($from, $email->getSubject(), $to, $content);

        if ($email->getReplyTos()) {
            foreach ($email->getReplyTos() as $recipient) {
                $mail->setReplyTo($this->mapEmail($recipient));
                break; // only one reply to
            }
        }

        if ($email->getCcs()) {
            foreach ($email->getCcs() as $recipient) {
                $personalization->addCc($this->mapEmail($recipient));
            }
        }

        if ($email->getBccs()) {
            foreach ($email->getBccs() as $recipient) {
                $personalization->addBcc($this->mapEmail($recipient));
            }
        }

        if ($email->getAttachments()) {
            foreach ($email->getAttachments() as $attachment) {
                $finalAttachment = new SendGridAttachment();
                $finalAttachment->setType($attachment->getMimeType());
                $finalAttachment->setFilename($attachment->getName());
                if (!$attachment->getPath() && $attachment->getContent()) {
                    $finalAttachment->setContent(base64_encode($attachment->getContent()));
                } elseif ($attachment->getPath()) {
                    $finalAttachment->setContent(base64_encode(file_get_contents($attachment->getPath())));
                }
                if ($attachment->getContentId()) {
                    $finalAttachment->setContentID($attachment->getContentId());
                    $finalAttachment->setDisposition($attachment->getDisposition());
                }
                $mail->addAttachment($finalAttachment);
            }
        }

        $mail->addPersonalization($personalization);
        $sg = new \SendGrid($this->apiKey);
        /** @var Response $response */
        $response = $sg->client->mail()->send()->post($mail);

        if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
            }
        } else {
            $content = json_decode($response->body(), true);
            $error = null;
            if (isset($content['errors']) && is_array($content['errors']) && isset($content['errors'][0])) {
                $error = $content['errors'][0]['message'];
            }
            switch ($response->statusCode()) {
                case 401:
                    if ($this->logger) {
                        $this->logger->info("Email error: 'unauthorized'", $email->toArray());
                    }
                    throw new UnauthorizedException($error);
                default:
                    if ($this->logger) {
                        $this->logger->info("Email error: 'invalid request'", $email->toArray());
                    }
                    throw new InvalidRequestException($error);
            }
        }
    }

    /**
     * @param $email
     * @return SendGridEmail
     */
    private function mapEmail($email)
    {
        return new SendGridEmail(isset($email['name']) ? $email['name'] : null, $email['email']);
    }
}
