<?php

namespace Omnimail\Tests;

use Omnimail\Email;
use Omnimail\Mandrill;

class MandrillTest extends BaseTestClass
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->expectException(\Mandrill_Invalid_Key::class);

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
