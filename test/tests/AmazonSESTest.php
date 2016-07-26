<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\AmazonSES;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class AmazonSESTest extends PHPUnit_Framework_TestCase
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->setExpectedException('Omnimail\Exception\Exception');

        $accessKey = 'ACCESSKEY';
        $secretKey = 'SECRETKEY';

        $sender = new AmazonSES($accessKey, $secretKey);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
    /*
    public function testRequestIsGeneratedCorrectly()
    {
        $apiKey = 'apikey';
        $domain = 'domain';

        $mock = new MockHandler([
            new Response(200, [], '<SendEmailResponse xmlns="https://email.amazonaws.com/doc/2010-03-31/">
              <SendEmailResult>
                <MessageId>000001271b15238a-fd3ae762-2563-11df-8cd4-6d4e828a9ae8-000000</MessageId>
              </SendEmailResult>
              <ResponseMetadata>
                <RequestId>fd3ae762-2563-11df-8cd4-6d4e828a9ae8</RequestId>
              </ResponseMetadata>
            </SendEmailResponse>'),
        ]);

        $stack = HandlerStack::create($mock);

        $sender = new AmazonSES($apiKey, $domain, null, null, $stack);

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
    }*/
}
