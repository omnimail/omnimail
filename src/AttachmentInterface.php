<?php

namespace Omnimail;

interface AttachmentInterface
{
    public function setPath($path);
    public function getPath();
    public function setContent($content);
    public function getContent();
    public function setName($name);
    public function getName();
    public function setMimeType($mimeType);
    public function getMimeType();
}
