<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\Mailgun;

class MailgunTest extends PHPUnit_Framework_TestCase
{
    public function test_error_message_is_thrown_with_incorrect_details()
    {
        $this->setExpectedException('Omnimail\Exception\Exception');

        $apiKey = 'apikey';
        $domain = 'domain';

        $sender = new Mailgun($apiKey, $domain);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
