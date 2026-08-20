<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use Omnimail\Email;
use Omnimail\Mailjet;
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
}
