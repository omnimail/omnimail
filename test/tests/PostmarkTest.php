<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use PHPUnit\Framework\TestCase;
use Omnimail\Email;
use Omnimail\Postmark;

class PostmarkTest extends TestCase
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $apiToken = 'apitoken';

        $sender = new Postmark($apiToken);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
