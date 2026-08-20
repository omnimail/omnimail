<?php

namespace Omnimail;

use Http\Client\HttpClient;
use Mailgun\Messages\Exceptions\InvalidParameter;
use Mailgun\Message\MessageBuilder;
use Omnimail\Exception\EmailDeliveryException;
use Omnimail\Exception\Exception;
use Omnimail\Exception\InvalidRequestException;
use Omnimail\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Mailgun\Mailgun as MailgunAPI;
use Mailgun\HttpClient\HttpClientConfigurator;
use GuzzleHttp\Client;

class Mailgun implements MailerInterface
{
    protected $apiKey;
    protected $domain;
    protected $mailgun;
    protected $logger;
    protected $httpClient;
    protected $tmpfiles = [];

    /**
     * @param string $apiKey
     * @param string $domain
     * @param LoggerInterface|null $logger
     * @param HttpClient $httpClient
     */
    public function __construct(
        $apiKey = null,
        $domain = null,
        ?LoggerInterface $logger = null,
        ?HttpClient $httpClient = null
    ) {
        $this->apiKey = $apiKey;
        $this->domain = $domain;
        $this->logger = $logger;
        if (!$httpClient) {
            $httpClient = new Client(['handler' => \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\CurlHandler())]);
        }
        $this->httpClient = $httpClient;
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
        $this->mailgun = new MailgunAPI($this->apiKey, $this->httpClient);
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
        $this->mailgun = new MailgunAPI($this->apiKey, $this->httpClient);
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

            if ($email->getAttachments()) {
                $this->mapAttachments($email->getAttachments(), $builder);
                $this->mapInlineAttachments($email->getAttachments(), $builder);
            }

            $this->mailgun->messages()->send($this->domain, $builder->getMessage());

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
        } finally {
            $this->removeTmpfiles();
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
     * @param AttachmentInterface[]|array|null $attachments
     * @param MessageBuilder $builder
     * @return array|null
     */
    private function mapAttachments(array $attachments, MessageBuilder $builder)
    {
        foreach ($attachments as $attachment) {
            if ($attachment->getContentId()) {
                continue;
            }

            if ($attachment->getPath()) {
                $file = $attachment->getPath();
            } elseif ($attachment->getContent()) {
                $this->addTmpfile($file = tmpfile());
                fwrite($file, $attachment->getContent());
            } else {
                continue;
            }
            $builder->addAttachment($file, $attachment->getName());
        }

        return null;
    }

    private function addTmpfile($file)
    {
        $this->tmpfiles[] = $file;
    }

    /**
     * @param AttachmentInterface[]|array|null $attachments
     * @param MessageBuilder $builder
     * @return void
     * @throws InvalidParameter
     */
    private function mapInlineAttachments(array $attachments, MessageBuilder $builder)
    {
        foreach ($attachments as $attachment) {
            if (!$attachment->getContentId()) {
                continue;
            }

            if ($attachment->getPath()) {
                $file = $attachment->getPath();
            } elseif ($attachment->getContent()) {
                $this->addTmpfile($file = tmpfile());
                fwrite($file, $attachment->getContent());
            } else {
                continue;
            }
            $builder->addInlineImage($file, $attachment->getContentId());
        }
    }

    private function removeTmpfiles()
    {
        foreach ($this->tmpfiles as $file) {
            fclose($file);
        }
    }
}
