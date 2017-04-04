<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use Omnimail\Email;
use Omnimail\Tests\Mock\Mailgun;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class MailgunTest extends BaseTestClass
{
    public function testRequestIsGeneratedCorrectly()
    {
        $apiKey = 'apikey';
        $domain = 'domain';

        $mock = new MockHandler([
            new Response(200, [], '{
              "message": "Queued. Thank you.",
              "id": "<20111114174239.25659.5817@samples.mailgun.org>"
            }'),
        ]);

        $stack = HandlerStack::create($mock);

        $sender = new Mailgun($apiKey, $domain, null, $stack);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);

        $this->assertEquals('https://api.mailgun.net/v2/domain/messages', $mock->getLastRequest()->getUri());
        $this->assertEquals('POST', $mock->getLastRequest()->getMethod());

        $dataToCheck = [
            'your@email.com',
            'from@email.com',
            'Hello, world!',
            'Hello World! How are you?'
        ];

        $requestContent = $mock->getLastRequest()->getBody()->getContents();

        foreach ($dataToCheck as $data) {
            $this->assertContains($data, $requestContent);
        }
    }

    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $apiKey = 'apikey';
        $domain = 'domain';

        $sender = new Mailgun($apiKey, $domain);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
