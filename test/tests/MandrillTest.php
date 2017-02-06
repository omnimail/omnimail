<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use PHPUnit\Framework\TestCase;
use Omnimail\Email;
use Omnimail\Mandrill;

class MandrillTest extends TestCase
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $apiKey = 'apikey';

        $sender = new Mandrill($apiKey);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
