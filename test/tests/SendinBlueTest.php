<?php

namespace Omnimail\Tests;

use PHPUnit_Framework_TestCase;
use Omnimail\Email;
use Omnimail\SendinBlue;

class SendinBlueTest extends PHPUnit_Framework_TestCase
{
    public function test_email_is_sent()
    {
        $accessKey = 'apikey';

        $sender = new SendinBlue($accessKey);

        $email = (new Email())
            ->addTo('your@email.com')
            ->setFrom('from@email.com')
            ->setSubject('Hello, world!')
            ->setTextBody('Hello World! How are you?');

        $sender->send($email);
    }
}
