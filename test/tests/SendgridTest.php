<?php

namespace Omnimail\Tests;

use Omnimail\Email;
use Omnimail\Sendgrid;
use Omnimail\Exception\Exception;

class SendgridTest extends BaseTestClass
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $apiKey = 'apikey';

        $sender = new Sendgrid($apiKey);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
