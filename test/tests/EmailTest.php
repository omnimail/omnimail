<?php

namespace Omnimail\Tests;

use Omnimail\Email;

class EmailTest extends BaseTestClass
{
    public function testToArray()
    {
        $email = new Email();
        $email->setTextBody('test1');
        $email->setHtmlBody('test2');
        $email->setFrom('test3', 'test4');
        $email->setSubject('test5');
        $email->addTo('test6', 'test7');
        $email->addReplyTo('test8', 'test9');
        $email->addCc('test10', 'test11');
        $email->addBcc('test12', 'test13');

        $array = $email->toArray();

        $this->assertEquals('test1', $array['textBody']);
        $this->assertEquals('test2', $array['htmlBody']);
        $this->assertEquals('test3', $array['from']['email']);
        $this->assertEquals('test4', $array['from']['name']);
        $this->assertEquals('test5', $array['subject']);
        $this->assertEquals('test6', $array['tos'][0]['email']);
        $this->assertEquals('test7', $array['tos'][0]['name']);
        $this->assertEquals('test8', $array['replyTos'][0]['email']);
        $this->assertEquals('test9', $array['replyTos'][0]['name']);
        $this->assertEquals('test10', $array['ccs'][0]['email']);
        $this->assertEquals('test11', $array['ccs'][0]['name']);
        $this->assertEquals('test12', $array['bccs'][0]['email']);
        $this->assertEquals('test13', $array['bccs'][0]['name']);
    }
}
