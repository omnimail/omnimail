<?php

namespace Omnimail\Tests;

use Omnimail\Exception\Exception;
use Omnimail\Email;
use Omnimail\Omnimail;

class AmazonSESTest extends BaseTestClass
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(Exception::class);

        $sender = Omnimail::create(Omnimail::AMAZONSES, [
            'accessKey' => 'ACCESSKEY',
            'secretKey' => 'SECRETKEY',
        ]);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
