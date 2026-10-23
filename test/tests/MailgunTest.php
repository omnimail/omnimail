<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use Omnimail\Attachment;
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
            new Response(200, ['Content-Type' => 'application/json'], '{
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

        $this->assertEquals('https://api.mailgun.net/v3/domain/messages', $mock->getLastRequest()->getUri());
        $this->assertEquals('POST', $mock->getLastRequest()->getMethod());

        $dataToCheck = [
            'your@email.com',
            'from@email.com',
            'Hello, world!',
            'Hello World! How are you?'
        ];

        $requestContent = $mock->getLastRequest()->getBody()->getContents();

        foreach ($dataToCheck as $data) {
            $this->assertStringContainsString($data, $requestContent);
        }
    }

    public function testSetApiKeyRebuildsClient()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{
              "message": "Queued. Thank you.",
              "id": "<20111114174239.25659.5817@samples.mailgun.org>"
            }'),
        ]);

        $sender = new Mailgun('oldkey', 'domain', null, HandlerStack::create($mock));
        $sender->setApiKey('newkey');

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);

        $this->assertEquals(
            'Basic ' . base64_encode('api:newkey'),
            $mock->getLastRequest()->getHeaderLine('Authorization')
        );
    }

    public function testContentAttachmentsAreSent()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{
              "message": "Queued. Thank you.",
              "id": "<20111114174239.25659.5817@samples.mailgun.org>"
            }'),
        ]);

        $sender = new Mailgun('apikey', 'domain', null, HandlerStack::create($mock));

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setHtmlBody('<img src="cid:logo-cid">')
            ->addAttachment((new Attachment())->setName('report.txt')->setContent('attachment-content'))
            ->addAttachment((new Attachment())->setContentId('logo-cid')->setContent('inline-content'));

        $sender->send($email);

        $requestContent = (string) $mock->getLastRequest()->getBody();

        $this->assertStringContainsString('attachment-content', $requestContent);
        $this->assertStringContainsString('inline-content', $requestContent);
        $this->assertMatchesRegularExpression('/name="attachment";\s*filename="report\.txt"/', $requestContent);
        $this->assertMatchesRegularExpression('/name="inline";\s*filename="logo-cid"/', $requestContent);
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
