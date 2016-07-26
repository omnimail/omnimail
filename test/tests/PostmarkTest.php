<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\Postmark;

class PostmarkTest extends PHPUnit_Framework_TestCase
{
    public function testErrorMessageIsThrownWithIncorrectDetails()
    {
        $this->setExpectedException('Omnimail\Exception\Exception');

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
