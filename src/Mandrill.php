<?php

namespace Omnimail;

use Mandrill_Error;
use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Psr\Log\LoggerInterface;

class Mandrill implements MailerInterface
{
    private $apiKey;
    private $ipPool;
    private $logger;
    private $mandrill;

    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param $apiKey
     * @throws Mandrill_Error
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->mandrill = new \Mandrill($apiKey);
    }

    public function getIpPool()
    {
        return $this->ipPool;
    }

    public function setIpPool($ipPool)
    {
        $this->ipPool = $ipPool;
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
     * @param string $ipPool
     * @param LoggerInterface|null $logger
     * @throws Mandrill_Error
     */
    public function __construct($apiKey = null, $ipPool = null, LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->ipPool = $ipPool;
        $this->logger = $logger;
        $this->mandrill = new \Mandrill($apiKey);
    }

    /**
     * @param EmailInterface $email
     * @throws EmailDeliveryException
     * @throws Exception
     * @throws InvalidRequestException
     */
    public function send(EmailInterface $email)
    {
        try {
            $from = $email->getFrom();

            $message = [
                'subject' => $email->getSubject(),
                'from_email' => $from['email'],
                'to' => $this->mapEmails($email->getTos())
            ];

            if (!empty($from['name'])) {
                $message['from_name'] = $from['name'];
            }

            if ($email->getReplyTos()) {
                $message['headers'] = [
                    'Reply-To' => $this->mapEmailsString($email->getReplyTos())
                ];
            }

            if ($email->getCcs()) {
                $message['to'] = array_merge(
                    $message['to'],
                    $this->mapEmails($email->getCcs(), 'cc')
                );
            }

            if ($email->getBccs()) {
                $message['to'] = array_merge(
                    $message['to'],
                    $this->mapEmails($email->getBccs(), 'bcc')
                );
            }

            if ($email->getTextBody()) {
                $message['text'] = $email->getTextBody();
            }

            if ($email->getHtmlBody()) {
                $message['html'] = $email->getHtmlBody();
            }

            if ($email->getAttachments()) {
                $message['attachments'] = $this->mapAttachments($email->getAttachments());
                $message['images'] = $this->mapInlineAttachments($email->getAttachments());
            }

            $result = $this->mandrill->messages->send($message, false, $this->ipPool);
            $result = current($result);
            if ($result && $result['status'] &&
                ($result['status'] === 'sent' || $result['status'] === 'queued' || $result['status'] === 'scheduled')
            ) {
                if ($this->logger) {
                    $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
                }
            } else {
                if (!$result || !$result['status']) {
                    $result = ['status' => 'invalid'];
                }
                if (!$result || !$result['reject_reason']) {
                    $result = ['reject_reason' => 'Unknown'];
                }
                switch ($result['status']) {
                    case 'invalid':
                        throw new InvalidRequestException($result['reject_reason']);
                    case 'rejected':
                        throw new EmailDeliveryException($result['reject_reason']);
                }
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            throw $e;
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
            'type' => $attachment->getMimeType(),
            'name' => $attachment->getName()
        ];
        if ($attachment->getPath()) {
            $finalAttachment['content'] = base64_encode(file_get_contents($attachment->getPath()));
        } elseif ($attachment->getContent()) {
            $finalAttachment['content'] = base64_encode($attachment->getContent());
        } else {
            return null;
        }

        if ($attachment->getContentId()) {
            $finalAttachment['name'] = $attachment->getContentId();
        }

        return $finalAttachment;
    }

    /**
     * @param array $emails
     * @param string $type
     * @return array
     */
    private function mapEmails(array $emails, $type = 'to')
    {
        $returnValue = [];
        foreach ($emails as $email) {
            $returnValue[] = $this->mapEmail($email, $type);
        }
        return $returnValue;
    }

    /**
     * @param array $email
     * @param string $type
     * @return array
     */
    private function mapEmail(array $email, $type = 'to')
    {
        $returnValue = ['email' => $email['email'], 'type' => $type];
        if ($email['name']) {
            $returnValue['name'] = $email['name'];
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
            $returnValue .= $this->mapEmailString($email).', ';
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
