<?php

namespace Omnimail;

use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Psr\Log\LoggerInterface;
use Sendinblue\Mailin;

class SendinBlue implements MailerInterface
{
    private $accessKey;
    private $logger;

    public function getAccessKey()
    {
        return $this->accessKey;
    }

    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
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
     * @param string $accessKey
     * @param LoggerInterface|null $logger
     */
    public function __construct($accessKey = null, LoggerInterface $logger = null)
    {
        $this->accessKey = $accessKey;
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {
        $mailin = new Mailin('https://api.sendinblue.com/v2.0', $this->accessKey);

        $data = [
            'to' => $this->mapEmails($email->getTos()),
            'cc' => $this->mapEmails($email->getCcs()),
            'bcc' => $this->mapEmails($email->getBccs()),
            'from' => $this->mapEmail($email->getFrom()),
            'replyto' => $this->mapEmails($email->getReplyTos()),
            'subject' => $email->getSubject(),
            'text' => $email->getTextBody(),
            'html' => $email->getHtmlBody(),
            'attachment' => $this->mapAttachments($email->getAttachments()),
            'inline_image' => $this->mapInlineImages($email->getAttachments())
        ];

        $response = $mailin->send_email($data);
        if ($response && $response['code'] && $response['code'] === 'success') {
            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
            }
        } else {
            if (!$response || !$response['code']) {
                throw new Exception('Unknown exception');
            } else {
                switch ($response['code']) {
                    case 'failure':
                        if ($this->logger) {
                            $this->logger->info("Email error: '{$response['message']}'", $email->toArray());
                        }
                        throw new InvalidRequestException($response['message']);
                    case 'error':
                        if ($this->logger) {
                            $this->logger->info("Email error: '{$response['message']}'", $email->toArray());
                        }
                        throw new InvalidRequestException($response['message']);
                }
            }
        }
    }

    /**
     * @param AttachmentInterface[] $attachments
     * @return array|null
     */
    private function mapAttachments(array $attachments = null)
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

            $content = null;
            if (!$attachment->getPath() && $attachment->getContent()) {
                $content = base64_encode($attachment->getContent());
            } elseif ($attachment->getPath()) {
                $content = base64_encode(file_get_contents($attachment->getPath()));
            }
            if ($content) {
                $finalAttachments[$attachment->getName()] = $content;
            }
        }
        return $finalAttachments;
    }

    /**
     * @param AttachmentInterface[] $attachments
     * @return array|null
     */
    private function mapInlineImages(array $attachments = null)
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

            $content = null;
            if (!$attachment->getPath() && $attachment->getContent()) {
                $content = base64_encode($attachment->getContent());
            } elseif ($attachment->getPath()) {
                $content = base64_encode(file_get_contents($attachment->getPath()));
            }
            if ($content) {
                $finalAttachments[$attachment->getContentId()] = $content;
            }
        }
        return $finalAttachments;
    }

    /**
     * @param array|null $emails
     * @return array|null
     */
    private function mapEmails(array $emails = null)
    {
        if (null === $emails || !is_array($emails) || !count($emails)) {
            return null;
        }
        $finalEmails = [];
        foreach ($emails as $email) {
            $finalEmails = array_merge($finalEmails, $this->mapEmail($email));
        }
        return $finalEmails;
    }

    /**
     * @param array $email
     * @return array
     */
    private function mapEmail(array $email)
    {
        $finalEmail = [];
        if ($email['name']) {
            $finalEmail[$email['email']] = $email['name'];
        } else {
            $finalEmail[$email['email']] = '';
        }
        return $finalEmail;
    }
}
