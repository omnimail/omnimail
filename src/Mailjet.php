<?php

namespace Omnimail;

use Omnimail\Exception\InvalidRequestException;
use Psr\Log\LoggerInterface;
use Mailjet\Resources;
use Mailjet\Client;

class Mailjet implements EmailSenderInterface
{
    private $apikey;
    private $apisecret;
    private $logger;
    private $mailjet;

    /**
     * @param string $apikey
     * @param string $apisecret
     * @param LoggerInterface|null $logger
     */
    public function __construct($apikey, $apisecret, LoggerInterface $logger = null)
    {
        $this->apikey = $apikey;
        $this->apisecret = $apisecret;
        $this->logger = $logger;
        $this->mailjet = new Client($apikey, $apisecret);
    }

    public function send(EmailInterface $email)
    {
        $from = $email->getFrom();

        $body = [
            'FromEmail' => $from['email'],
            'Subject' => $email->getSubject(),
            'To' => $this->mapEmails($email->getTo())
        ];

        if (!empty($from['name'])) {
            $body['FromName'] = $from['name'];
        }

        if ($email->getReplyTo()) {
            $body['Headers'] = [
                'Reply-To' => $this->mapEmailsString($email->getReplyTo())
            ];
        }

        if ($email->getCc()) {
            $body['Cc'] = $this->mapEmails($email->getCc());
        }

        if ($email->getBcc()) {
            $body['Bcc'] = $this->mapEmails($email->getBcc());
        }

        if ($email->getTextBody()) {
            $body['Text-part'] = $email->getTextBody();
        }

        if ($email->getHtmlBody()) {
            $body['Html-part'] = $email->getHtmlBody();
        }

        if ($email->getAttachements()) {
            $attachements = [];
            foreach ($email->getAttachements() as $attachement) {
                $item = [
                    'Content-type' => $attachement->getMimeType(),
                    'Filename' => $attachement->getName()
                ];
                if (!$attachement->getPath() && $attachement->getContent()) {
                    $item['content'] = base64_encode($attachement->getContent());
                } elseif ($attachement->getPath()) {
                    $item['content'] = base64_encode(file_get_contents($attachement->getPath()));
                }
                $attachements[] = $item;
            }
            $body['Attachments'] = $attachements;
        }

        $response = $this->mailjet->post(Resources::$Email, ['body' => $body]);

        if ($response->success()) {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email);
            }
        } else {
            if ($this->logger) {
                $this->logger->error("Email error: '{$response->getReasonPhrase()}'", $email);
            }
            throw new InvalidRequestException($response->getReasonPhrase());
        }
    }

    /**
     * @param array $emails
     * @return array
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
     * @return array
     */
    private function mapEmail(array $email)
    {
        $returnValue = ['Email' => $email['email']];
        if ($email['name']) {
            $returnValue['Name'] = $email['name'];
        }
        return $returnValue;
    }

    /**
     * @param array $emails
     * @return string
     */
    private function mapEmailsString(array $emails)
    {
        $returnValue = '';
        foreach ($emails as $email) {
            $returnValue .= $this->mapEmailString($email) . ', ';
        }
        return $returnValue ? substr($returnValue, 0, -2) : '';
    }

    /**
     * @param array $email
     * @return string
     */
    private function mapEmailString(array $email)
    {
        return !empty($email['name']) ? "'{$email['name']}' <{$email['email']}>" : $email['email'];
    }
}
