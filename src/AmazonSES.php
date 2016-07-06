<?php

namespace Omnimail;

use Omnimail\Exception\Exception;
use Psr\Log\LoggerInterface;
use SimpleEmailServiceMessage;
use SimpleEmailService;

class AmazonSES implements EmailSenderInterface
{
    const AWS_US_EAST_1 = 'email.us-east-1.amazonaws.com';
    const AWS_US_WEST_2 = 'email.us-west-2.amazonaws.com';
    const AWS_EU_WEST1 = 'email.eu-west-1.amazonaws.com';

    private $accessKey;
    private $secretKey;
    private $host;
    private $logger;

    /**
     * @param string $accessKey
     * @param string $secretKey
     * @param string $host
     * @param LoggerInterface|null $logger
     */
    public function __construct($accessKey, $secretKey, $host = self::AWS_US_EAST_1, LoggerInterface $logger = null)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->host = $host;
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {
        $m = new SimpleEmailServiceMessage();
        $m->addTo($this->mapEmails($email->getTo()));
        $m->setFrom($this->mapEmail($email->getFrom()));

        if ($email->getReplyTo()) {
            $m->addReplyTo($this->mapEmails($email->getReplyTo()));
        }

        if ($email->getCc()) {
            $m->addCC($this->mapEmails($email->getCc()));
        }

        if ($email->getBcc()) {
            $m->addBCC($this->mapEmails($email->getBcc()));
        }

        $m->setSubject($email->getSubject());
        $m->setMessageFromString($email->getTextBody(), $email->getHtmlBody());

        if ($email->getAttachements()) {
            foreach ($email->getAttachements() as $attachement) {
                if (!$attachement->getPath() && $attachement->getContent()) {
                    $m->addAttachmentFromData(
                        $attachement->getName(),
                        $attachement->getContent(),
                        $attachement->getMimeType()
                    );
                } elseif ($attachement->getPath()) {
                    $m->addAttachmentFromFile(
                        $attachement->getName(),
                        $attachement->getPath(),
                        $attachement->getMimeType()
                    );
                }
            }
        }

        $ses = new SimpleEmailService($this->accessKey, $this->secretKey, $this->host, false);
        $response = $ses->sendEmail($m, false, false);

        if (empty($response['MessageId'])) {
            if ($this->logger) {
                $this->logger->error("Email error: Unknown error", $email);
            }
            throw new Exception('Unknown error', 603);
        } else {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email);
            }
        }
    }

    /**
     * @param array $emails
     * @return string
     */
    private function mapEmails(array $emails)
    {
        $returnValue = [];
        foreach ($emails as $email) {
            $returnValue[] = $this->mapEmail($email);
        }
        return $returnValue;
    }

    /**
     * @param array $email
     * @return string
     */
    private function mapEmail(array $email)
    {
        return !empty($email['name']) ? "'{$email['name']}' <{$email['email']}>" : $email['email'];
    }
}
