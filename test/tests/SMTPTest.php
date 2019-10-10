<?php

namespace Omnimail\Tests;

use Omnimail\Email;
use Omnimail\Exception\Exception;
use Omnimail\SMTP;

class SMTPTest extends BaseTestClass
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $username = 'testusername';
        $password = 'testpassword';
        $smtpHost = 'example.com';

        $sender = new SMTP($smtpHost, $username, $password, ['timeout' => 10]);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
