<?php

namespace Omnimail;

use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;

class Postmark implements EmailSenderInterface
{
    private $serverApiToken;
    private $logger;

    /**
     * @param string $serverApiToken
     * @param LoggerInterface|null $logger
     */
    public function __construct($serverApiToken, LoggerInterface $logger = null)
    {
        $this->serverApiToken = $serverApiToken;
        $this->logger = $logger;
    }

    public function send(EmailInterface $email)
    {
        try {
            $client = new PostmarkClient($this->serverApiToken);
            $sendResult = $client->sendEmail(
                $this->mapEmail($email->getFrom()),
                $this->mapEmails($email->getTo()),
                $email->getSubject(),
                $email->getHtmlBody(),
                $email->getTextBody(),
                null,
                true,
                $this->mapEmails($email->getReplyTo()),
                $this->mapEmails($email->getCc()),
                $this->mapEmails($email->getBcc()),
                null,
                $this->mapAttachments($email->getAttachements())
            );

            if (!isset($sendResult) || (!isset($sendResult[0]) && !isset($sendResult['ErrorCode']))) {
                throw new InvalidRequestException;
            } elseif (!isset($sendResult['ErrorCode']) && isset($sendResult[0])) {
                $sendResult = $sendResult[0];
            }

            if ((int) $sendResult['ErrorCode'] !== 0) {
                $e = new PostmarkException();
                $e->httpStatusCode = $sendResult['ErrorCode'];
                $e->message = $sendResult['Message'];
                $e->postmarkApiErrorCode = $sendResult['ErrorCode'];
                throw $e;
            }

            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email);
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email);
            }
            throw $e;
        } catch (PostmarkException $e) {
            if ($this->logger) {
                $this->logger->info("Email error: '{$e->getMessage()}'", $email);
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
                $this->logger->info("Email error: '{$e->getMessage()}'", $email);
            }
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array|null $attachements
     * @return array|null
     */
    private function mapAttachments(array $attachements = null)
    {
        if (null === $attachements || !is_array($attachements) || !count($attachements)) {
            return null;
        }

        $finalAttachements = [];
        foreach ($attachements as $attachement) {
            $finalAttachement = [
                'ContentType' => $attachement->getMimeType(),
                'Name' => $attachement->getName()
            ];
            if (!$attachement->getPath() && $attachement->getContent()) {
                $finalAttachement['Content'] = base64_encode($attachement->getContent());
            } elseif ($attachement->getPath()) {
                $finalAttachement['Content'] = base64_encode(file_get_contents($attachement->getPath()));
            }
            $finalAttachements[] = $finalAttachement;
        }
        return $finalAttachements;
    }

    /**
     * @param array $emails
     * @return string
     */
    private function mapEmails(array $emails)
    {
        $returnValue = '';
        foreach ($emails as $email) {
            $returnValue .= $this->mapEmail($email) . ', ';
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
