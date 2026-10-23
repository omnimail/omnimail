<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use Omnimail\Email;
use Omnimail\Mailjet;
use Omnimail\Omnimail;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MailjetTest extends BaseTestClass
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $apiKey = 'apikey';
        $apiSecret = 'secret';

        $mockClient = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new Response(401, [], '{}');
            }
        };

        $sender = new Mailjet($apiKey, $apiSecret, null, $mockClient);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }

    public function testFactoryCreatesMailer()
    {
        $sender = Omnimail::create('Mailjet', ['apiKey' => 'apikey', 'apiSecret' => 'secret']);

        $this->assertInstanceOf(Mailjet::class, $sender);
        $this->assertEquals('apikey', $sender->getApiKey());
        $this->assertEquals('secret', $sender->getApiSecret());
    }

    public function testSettersAreUsedForAuthentication()
    {
        $mockClient = new class implements ClientInterface {
            public $lastRequest;
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->lastRequest = $request;
                return new Response(200, ['Content-Type' => 'application/json'], '{}');
            }
        };

        $sender = new Mailjet('oldkey', 'oldsecret', null, $mockClient);
        $sender->setApiKey('newkey');
        $sender->setApiSecret('newsecret');

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);

        $this->assertEquals(
            'Basic ' . base64_encode('newkey:newsecret'),
            $mockClient->lastRequest->getHeaderLine('Authorization')
        );
    }
}
