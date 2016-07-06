<?php

namespace Omnimail;

interface AttachmentInterface
{
    /**
     * @param string|null $path
     * @return AttachmentInterface
     */
    public function setPath($path = null);

    /**
     * @return string|null
     */
    public function getPath();

    /**
     * @param string|null $content
     * @return AttachmentInterface
     */
    public function setContent($content);

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @param string|null $name
     * @return AttachmentInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $mimeType
     * @return AttachmentInterface
     */
    public function setMimeType($mimeType);

    /**
     * @return string|null
     */
    public function getMimeType();
}
