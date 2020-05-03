<?php

namespace Omnimail\Tests;

use Omnimail\Attachment;
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

        $attachment = new Attachment();
        $attachment->setName('my_file.txt');
        $attachment->setMimeType('text/plain');
        $attachment->setContent('This is plain text');

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?')
            ->addAttachment($attachment);

        $sender->send($email);
    }
}
