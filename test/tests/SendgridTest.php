<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\Sendgrid;

class SendgridTest extends PHPUnit_Framework_TestCase
{
    public function test_email_is_sent()
    {
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
