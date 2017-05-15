<?php

namespace Omnimail;

use Omnimail\Exception\InvalidRequestException;
use Psr\Log\LoggerInterface;
use Mailjet\Resources;
use Mailjet\Client;

class Mailjet implements MailerInterface
{
    private $apikey;
    private $apisecret;
    private $logger;
    private $mailjet;

    public function getApiKey()
    {
        return $this->apikey;
    }

    public function setApiKey($apikey)
    {
        $this->apikey = $apikey;
        $this->mailjet = new Client($apikey, $this->apisecret);
    }

    public function getApiSecret()
    {
        return $this->apisecret;
    }

    public function setApiSecret($apisecret)
    {
        $this->apisecret = $apisecret;
        $this->mailjet = new Client($this->apikey, $apisecret);
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
     * @param string $apikey
     * @param string $apisecret
     * @param LoggerInterface|null $logger
     */
    public function __construct($apikey = null, $apisecret = null, LoggerInterface $logger = null)
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
            'Recipients' => $this->mapEmails($email->getTos())
        ];

        if (!empty($from['name'])) {
            $body['FromName'] = $from['name'];
        }

        if ($email->getReplyTos()) {
            $body['Headers'] = [
                'Reply-To' => $this->mapEmailsString($email->getReplyTos())
            ];
        }

        if ($email->getCcs()) {
            $body['Cc'] = $this->mapEmails($email->getCcs());
        }

        if ($email->getBccs()) {
            $body['Bcc'] = $this->mapEmails($email->getBccs());
        }

        if ($email->getTextBody()) {
            $body['Text-part'] = $email->getTextBody();
        }

        if ($email->getHtmlBody()) {
            $body['Html-part'] = $email->getHtmlBody();
        }

        if ($email->getAttachments()) {
            $body['Attachments'] = $this->mapAttachments($email->getAttachments());
            $body['Inline_attachments'] = $this->mapInlineAttachments($email->getAttachments());
        }

        $response = $this->mailjet->post(Resources::$Email, ['body' => $body]);

        if ($response->success()) {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
            }
        } else {
            if ($this->logger) {
                $this->logger->error("Email error: '{$response->getReasonPhrase()}'", $email->toArray());
            }
            throw new InvalidRequestException($response->getReasonPhrase());
        }
    }

    /**
     * @param AttachmentInterface[] $attachments
     * @return array|null
     */
    private function mapAttachments(array $attachments)
    {
        if (null === $attachments || !is_array($attachments) || !count($attachments)) {
            return null;
        }

        $finalAttachments = [];
        /** @var AttachmentInterface $attachment */
        foreach ($attachments as $attachment) {
            if ($attachment->getContentId()) {
                continue;
            }
            $finalAttachment = $this->mapAttachment($attachment);
            if ($finalAttachment) {
                $finalAttachments[] = $finalAttachment;
            }
        }
        return $finalAttachments;
    }

    /**
     * @param AttachmentInterface[] $attachments
     * @return array|null
     */
    private function mapInlineAttachments(array $attachments)
    {
        if (null === $attachments || !is_array($attachments) || !count($attachments)) {
            return null;
        }

        $finalAttachments = [];
        /** @var AttachmentInterface $attachment */
        foreach ($attachments as $attachment) {
            if (!$attachment->getContentId()) {
                continue;
            }
            $finalAttachment = $this->mapAttachment($attachment);
            if ($finalAttachment) {
                $finalAttachments[] = $finalAttachment;
            }
        }
        return $finalAttachments;
    }

    private function mapAttachment(AttachmentInterface $attachment)
    {
        $finalAttachment = [
            'Content-type' => $attachment->getMimeType(),
            'Filename' => $attachment->getName()
        ];
        if ($attachment->getPath()) {
            $finalAttachment['content'] = base64_encode(file_get_contents($attachment->getPath()));
        } elseif ($attachment->getContent()) {
            $finalAttachment['content'] = base64_encode($attachment->getContent());
        }

        if ($attachment->getContentId()) {
            $finalAttachment['Filename'] = $attachment->getContentId();
        }

        return $finalAttachment;
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
