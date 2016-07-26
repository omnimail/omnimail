<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\Mandrill;

class MandrillTest extends PHPUnit_Framework_TestCase
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->setExpectedException('Omnimail\Exception\Exception');

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
