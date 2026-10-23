<?php

namespace Omnimail;

use Http\Client\HttpClient;
use Mailgun\Message\MessageBuilder;
use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Mailgun\Mailgun as MailgunAPI;
use Mailgun\HttpClient\HttpClientConfigurator;

class Mailgun implements MailerInterface
{
    protected $apiKey;
    protected $domain;
    protected $mailgun;
    protected $logger;
    protected $httpClient;

    /**
     * @param string $apiKey
     * @param string $domain
     * @param LoggerInterface|null $logger
     * @param HttpClient|\Psr\Http\Client\ClientInterface|null $httpClient
     *   httplug or PSR-18 client. If null, Mailgun discovers an installed PSR-18 client.
     */
    public function __construct(
        $apiKey = null,
        $domain = null,
        ?LoggerInterface $logger = null,
        $httpClient = null
    ) {
        $this->apiKey = $apiKey;
        $this->domain = $domain;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->buildMailgun();
    }

    /**
     * (Re)create the Mailgun API client from the current api key and http client.
     */
    private function buildMailgun()
    {
        $configurator = (new HttpClientConfigurator())->setApiKey((string) $this->apiKey);
        if ($this->httpClient) {
            $psr18Client = $this->httpClient instanceof \Psr\Http\Client\ClientInterface
                ? $this->httpClient
                : new class($this->httpClient) implements \Psr\Http\Client\ClientInterface {
                    private $client;
                    public function __construct($client)
                    {
                        $this->client = $client;
                    }
                    public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
                    {
                        return method_exists($this->client, 'sendRequest')
                            ? $this->client->sendRequest($request)
                            : $this->client->send($request);
                    }
                };
            $configurator->setHttpClient($psr18Client);
        }
        $this->mailgun = new MailgunAPI($configurator);
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->buildMailgun();
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        $this->buildMailgun();
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
            $builder = new MessageBuilder();

            if ($email->getTos()) {
                foreach ($email->getTos() as $recipient) {
                    $builder->addToRecipient($this->mapEmails($email->getTos()));
                }
            }

            $builder->setFromAddress($this->mapEmail($email->getFrom()));

            if ($email->getReplyTos()) {
                $builder->setReplyToAddress($this->mapEmails($email->getReplyTos()));
            }

            if ($email->getCcs()) {
                foreach ($email->getCcs() as $recipient) {
                    $builder->addCcRecipient($this->mapEmail($recipient));
                }
            }

            if ($email->getBccs()) {
                foreach ($email->getBccs() as $recipient) {
                    $builder->addBccRecipient($this->mapEmail($recipient));
                }
            }

            if ($email->getSubject()) {
                $builder->setSubject($email->getSubject());
            }

            if ($email->getTextBody()) {
                $builder->setTextBody($email->getTextBody());
            }

            if ($email->getHtmlBody()) {
                $builder->setHtmlBody($email->getHtmlBody());
            }

            $message = $builder->getMessage();
            if ($email->getAttachments()) {
                $message = $this->mapAttachments($email->getAttachments(), $message);
            }

            $this->mailgun->messages()->send($this->domain, $message);

            if ($this->logger) {
                $this->logger->info("Email sent: '{$email->getSubject()}'", $email->toArray());
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            throw $e;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error("Email error: '{$e->getMessage()}'", $email->toArray());
            }
            throw new Exception($e->getMessage(), $e->getCode(), $e);
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

    /**
     * Add attachments to the message array in the format Mailgun's
     * Message::send() expects, using fileContent for in-memory attachments.
     *
     * @param AttachmentInterface[] $attachments
     * @param array $message
     * @return array
     */
    private function mapAttachments(array $attachments, array $message)
    {
        foreach ($attachments as $attachment) {
            // Mailgun uses the filename of an inline part as its Content-ID
            $field = $attachment->getContentId() ? 'inline' : 'attachment';
            $filename = $attachment->getContentId() ?: $attachment->getName();

            if ($attachment->getPath()) {
                $message[$field][] = ['filePath' => $attachment->getPath(), 'filename' => $filename];
            } elseif ($attachment->getContent()) {
                $message[$field][] = ['fileContent' => $attachment->getContent(), 'filename' => $filename];
            }
        }

        return $message;
    }
}
