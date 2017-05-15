<?php

namespace Omnimail\Tests\Common;

use Omnimail\Common\Helper;
use Omnimail\Mailgun;
use Omnimail\Omnimail;
use Omnimail\Tests\BaseTestClass;

class HelperTest extends BaseTestClass
{
    public function testCamelCase()
    {
        $this->assertEquals('testTest', Helper::camelCase('test_test'));
    }

    public function testInitialize()
    {
        $mailgun = new Mailgun();
        Helper::initialize($mailgun, ['domain' => 'test.com']);
        $this->assertEquals('test.com', $mailgun->getDomain());
    }

    public function testGetMailerClassName()
    {
        $this->assertEquals('\\Omnimail\\Mailgun', Helper::getMailerClassName('Mailgun'));
        $this->assertEquals('\\Omnimail\\Mailgun', Helper::getMailerClassName('\\Omnimail\\Mailgun'));
        $this->assertEquals('\\Omnimail\\Mailgun', Helper::getMailerClassName(Omnimail::MAILGUN));
    }
}
