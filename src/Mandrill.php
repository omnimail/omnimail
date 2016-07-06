<?php

namespace Omnimail;

use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Psr\Log\LoggerInterface;

class Mandrill implements EmailSenderInterface
{
    private $apiKey;
    private $ipPool;
    private $logger;
    private $mandrill;

    /**
     * @param string $apiKey
     * @param string $ipPool
     * @param LoggerInterface|null $logger
     */
    public function __construct($apiKey, $ipPool = null, LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->ipPool = $ipPool;
        $this->logger = $logger;
        $this->mandrill = new \Mandrill($apiKey);
    }

    public function send(EmailInterface $email)
    {
        try {
            $from = $email->getFrom();

            $message = [
                'subject' => 'example subject',
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

            if ($email->getAttachements()) {
                $attachements = [];
                foreach ($email->getAttachements() as $attachement) {
                    $item = [
                        'type' => $attachement->getMimeType(),
                        'name' => $attachement->getName()
                    ];
                    if (!$attachement->getPath() && $attachement->getContent()) {
                        $item['content'] = base64_encode($attachement->getContent());
                    } elseif ($attachement->getPath()) {
                        $item['content'] = base64_encode(file_get_contents($attachement->getPath()));
                    }
                    $attachements[] = $item;
                }
                $message['attachments'] = $attachements;
            }

            $result = $this->mandrill->messages->send($message, false, $this->ipPool);
            if ($result && $result['status'] &&
                ($result['status'] === 'sent' || $result['status'] === 'queued' || $result['status'] === 'scheduled')
            ) {
                if ($this->logger) {
                    $this->logger->info("Email sent: '{$email->getSubject()}'", $email);
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
                $this->logger->info("Email error: '{$e->getMessage()}'", $email);
            }
            throw $e;
        } catch (\Mandrill_Error $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email);
            }
            throw new InvalidRequestException($e->getMessage(), 601, $e);
        }
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
