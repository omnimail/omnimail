<?php

namespace Omnimail;

use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;

class Postmark implements MailerInterface
{
    private $serverApiToken;
    private $logger;

    public function getServerApiToken()
    {
        return $this->serverApiToken;
    }

    public function setServerApiToken($serverApiToken)
    {
        $this->serverApiToken = $serverApiToken;
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
     * @param string $serverApiToken
     * @param LoggerInterface|null $logger
     */
    public function __construct($serverApiToken = null, LoggerInterface $logger = null)
    {
        $this->serverApiToken = $serverApiToken;
        $this->logger = $logger;
    }

    /**
     * @param EmailInterface $email
     * @throws EmailDeliveryException
     * @throws Exception
     * @throws InvalidRequestException
     * @throws UnauthorizedException
     */
    public function send(EmailInterface $email)
    {
        try {
            $client = new PostmarkClient($this->serverApiToken);
            $sendResult = $client->sendEmail(
                $this->mapEmail($email->getFrom()),
                $this->mapEmails($email->getTos()),
                $email->getSubject(),
                $email->getHtmlBody(),
                $email->getTextBody(),
                null,
                true,
                $this->mapEmails($email->getReplyTos()),
                $this->mapEmails($email->getCcs()),
                $this->mapEmails($email->getBccs()),
                null,
                $this->mapAttachments($email->getAttachments())
            );

            if (!isset($sendResult) || (!isset($sendResult[0]) && !isset($sendResult['ErrorCode']))) {
                throw new InvalidRequestException;
            } elseif (!isset($sendResult['ErrorCode']) && isset($sendResult[0])) {
                $sendResult = $sendResult[0];
            }

            if ((int)$sendResult['ErrorCode'] !== 0) {
                $e = new PostmarkException();
                $e->httpStatusCode = $sendResult['ErrorCode'];
                $e->message = $sendResult['Message'];
                $e->postmarkApiErrorCode = $sendResult['ErrorCode'];
                throw $e;
            }

            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            throw $e;
        } catch (PostmarkException $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            switch ($e->postmarkApiErrorCode) {
                case 10:
                case 400:
                case 401:
                case 405:
                case 500:
                case 501:
                case 502:
                case 503:
                    throw new UnauthorizedException($e->message, 602, $e);
                case 100:
                case 411:
                    throw new EmailDeliveryException($e->message, 600, $e);
                default:
                    throw new InvalidRequestException($e->message, 601, $e);
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            throw new Exception($e->getMessage(), $e->getCode(), $e);
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
            $finalAttachment = [
                'ContentType' => $attachment->getMimeType(),
                'Name' => $attachment->getName()
            ];
            if (!$attachment->getPath() && $attachment->getContent()) {
                $finalAttachment['Content'] = base64_encode($attachment->getContent());
            } elseif ($attachment->getPath()) {
                $finalAttachment['Content'] = base64_encode(file_get_contents($attachment->getPath()));
            }

            if ($attachment->getContentId()) {
                $finalAttachment['ContentId'] = $attachment->getContentId();
            }

            $finalAttachments[] = $finalAttachment;
        }
        return $finalAttachments;
    }

    /**
     * @param array $emails
     * @return string
     */
    private function mapEmails(array $emails)
    {
        $returnValue = '';
        foreach ($emails as $email) {
            $returnValue .= $this->mapEmail($email).', ';
        }
        return $returnValue ? substr($returnValue, 0, -2) : '';
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
