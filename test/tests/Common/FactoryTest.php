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
        require_once __DIR__ . '/../includes/Swiftmailer/Mailer.php';
        $mail = Omnimail::create('Swiftmailer');
        $this->assertInstanceOf('Omnimail\Swiftmailer\Mailer', $mail);
    }
}
