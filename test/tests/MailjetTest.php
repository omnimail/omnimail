<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\Mailjet;

class MailjetTest extends PHPUnit_Framework_TestCase
{
    public function test_email_is_sent()
    {
        $apiKey = 'apikey';
        $apiSecret = 'secret';

        $sender = new Mailjet($apiKey, $apiSecret);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
