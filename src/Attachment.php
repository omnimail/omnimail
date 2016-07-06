<?php

namespace Omnimail;

class Attachment implements AttachmentInterface
{
    protected $path;
    protected $name;
    protected $content;
    protected $mimeType;

    /**
     * @param string|null $path
     * @return AttachmentInterface
     */
    public function setPath($path = null)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string|null $content
     * @return AttachmentInterface
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string|null $name
     * @return AttachmentInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        $name = $this->name;
        if (!$name && $this->path) {
            return basename($this->path);
        }
        return $name;
    }

    /**
     * @param string|null $mimeType
     * @return AttachmentInterface
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
