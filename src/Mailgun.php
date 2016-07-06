<?php

namespace Omnimail;

use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Mailgun\Mailgun as MailgunAPI;
use Http\Adapter\Guzzle6\Client;

class Mailgun implements EmailSenderInterface
{
    private $apiKey;
    private $domain;
    private $mailgun;
    private $logger;
    private $tmpfiles;

    /**
     * @param string $apiKey
     * @param string $domain
     * @param LoggerInterface|null $logger
     */
    public function __construct($apiKey, $domain, LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->domain = $domain;
        $this->logger = $logger;
        $this->mailgun = new MailgunAPI($this->apiKey, new Client());
    }

    public function send(EmailInterface $email)
    {
        try {
            $builder = $this->mailgun->MessageBuilder();
            $builder->setFromAddress($this->mapEmail($email->getFrom()));

            if ($email->getReplyTo()) {
                $builder->setReplyToAddress($this->mapEmails($email->getReplyTo()));
            }

            if ($email->getCc()) {
                foreach ($email->getCc() as $recipient) {
                    $builder->addCcRecipient($this->mapEmail($recipient));
                }
            }

            if ($email->getBcc()) {
                foreach ($email->getBcc() as $recipient) {
                    $builder->addBccRecipient($this->mapEmail($recipient));
                }
            }

            if ($email->getTextBody()) {
                $builder->setTextBody($email->getTextBody());
            }

            if ($email->getHtmlBody()) {
                $builder->setHtmlBody($email->getHtmlBody());
            }

            if ($email->getAttachements()) {
                foreach ($email->getAttachements() as $attachement) {
                    if (!$attachement->getPath() && $attachement->getContent()) {
                        $this->addTmpfile($file = tmpfile());
                        fwrite($file, $attachement->getContent());
                    } else {
                        $file = $attachement->getPath();
                    }
                    $builder->addAttachment($file, $attachement->getName());
                }
            }

            $result = $this->mailgun->post(
                "{$this->domain}/messages",
                $builder->getMessage(),
                $builder->getFiles()
            );

            switch ($result->http_response_code) {
                case 200:
                    break;
                case 400:
                    throw new InvalidRequestException;
                case 401:
                    throw new UnauthorizedException;
                case 402:
                    throw new EmailDeliveryException;
                default:
                    throw new Exception('Unknown error', 603);
            }

            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'");
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error("Email error: '{$e->getMessage()}'");
            }
            throw $e;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error("Email error: '{$e->getMessage()}'");
            }
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        } finally {
            $this->removeTmpfiles();
        }
    }

    private function addTmpfile($file)
    {
        $this->tmpfiles[] = $file;
    }

    private function removeTmpfiles()
    {
        foreach ($this->tmpfiles as $file) {
            fclose($file);
        }
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
