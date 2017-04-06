<?php

namespace Omnimail\Tests\Common;

use Omnimail\Omnimail;
use Omnimail\Tests\BaseTestClass;

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
