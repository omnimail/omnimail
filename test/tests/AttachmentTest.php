<?php

namespace Omnimail\Tests;

use Omnimail\Attachment;
use Omnimail\AttachmentInterface;

class AttachmentTest extends BaseTestClass
{
    public function testToArray()
    {
        $attachment = new Attachment();
        $attachment->setContent('test1');
        $attachment->setContentId('test2');
        $attachment->setDisposition(AttachmentInterface::DISPOSITION_SESSION);
        $attachment->setMimeType('test/test');
        $attachment->setName('test3');
        $attachment->setPath('test4');

        $array = $attachment->toArray();
        $this->assertEquals('test1', $array['content']);
        $this->assertEquals('test2', $array['contentId']);
        $this->assertEquals(AttachmentInterface::DISPOSITION_SESSION, $array['disposition']);
        $this->assertEquals('test/test', $array['mimeType']);
        $this->assertEquals('test3', $array['name']);
        $this->assertEquals('test4', $array['path']);
    }
}
