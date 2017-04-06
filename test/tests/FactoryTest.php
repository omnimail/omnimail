<?php

namespace Omnimail\Tests;

use Omnimail\Omnimail;

class FactoryTest extends BaseTestClass
{
    public function testFactoryLoad()
    {
        $mail = Omnimail::create('Mailgun');
        $this->assertInstanceOf('\Omnimail\Mailgun', $mail);
    }
    public function testFactoryLoadOther()
    {
        $mail = Omnimail::create('Swiftmailer');
        $this->assertInstanceOf('Omnimail\Swiftmailer\Mailer', $mail);
    }
}
