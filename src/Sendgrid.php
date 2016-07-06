<?php

namespace Omnimail;

use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use SendGrid\Email;
use SendGrid\Content;
use SendGrid\Mail;
use SendGrid\Attachment;
use SendGrid\Personalization;
use SendGrid\Response;

class Sendgrid implements EmailSenderInterface
{
    private $apiKey;
    private $logger;

    /**
     * @param string $apiKey
     * @param LoggerInterface|null $logger
     */
    public function __construct($apiKey, LoggerInterface $logger = null)
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

        $mail = new Mail(
            $this->mapEmail($email->getFrom()),
            $email->getSubject(),
            null,
            $content
        );

        /** @var Personalization $personalization */
        $personalization = $mail->personalization;

        foreach ($email->getTo() as $recipient) {
            $personalization->addTo($this->mapEmail($recipient));
        }

        if ($email->getReplyTo()) {
            foreach ($email->getReplyTo() as $recipient) {
                $mail->setReplyTo($this->mapEmail($recipient));
                break; // only one reply to
            }
        }

        if ($email->getCc()) {
            foreach ($email->getCc() as $recipient) {
                $personalization->addCc($this->mapEmail($recipient));
            }
        }

        if ($email->getBcc()) {
            foreach ($email->getBcc() as $recipient) {
                $personalization->addBcc($this->mapEmail($recipient));
            }
        }

        if ($email->getAttachements()) {
            foreach ($email->getAttachements() as $attachement) {
                $finalAttachment = new Attachment();
                $finalAttachment->setType($attachement->getMimeType());
                $finalAttachment->setFilename($attachement->getName());
                if (!$attachement->getPath() && $attachement->getContent()) {
                    $finalAttachment->setContent(base64_encode($attachement->getContent()));
                } elseif ($attachement->getPath()) {
                    $finalAttachment->setContent(base64_encode(file_get_contents($attachement->getPath())));
                }
                $mail->addAttachment($finalAttachment);
            }
        }

        $sg = new \SendGrid($this->apiKey);
        /** @var Response $response */
        $response = $sg->client->mail()->send()->post($mail);

        if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email);
            }
        } else {
            switch ($response->statusCode()) {
                case 401:
                    if ($this->logger) {
                        $this->logger->info("Email error: 'unauthorized'", $email);
                    }
                    throw new UnauthorizedException;
                default:
                    if ($this->logger) {
                        $this->logger->info("Email error: 'invalid request'", $email);
                    }
                    throw new InvalidRequestException;
            }
        }
    }

    /**
     * @param $email
     * @return Email
     */
    private function mapEmail($email)
    {
        return new Email(isset($email['name']) ? $email['name'] : null, $email['email']);
    }
}
