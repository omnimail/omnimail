<?php

namespace Omnimail;

use Omnimail\Utils\Token;

class Attachment implements AttachmentInterface
{
    protected $path;
    protected $name;
    protected $content;
    protected $mimeType;
    protected $contentId;
    protected $disposition;

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

    /**
     * @return AttachmentInterface
     */
    public function generateContentId()
    {
        if ($this->path) {
            $this->setContentId(Token::generate(4).'_'.basename($this->path));
            return $this;
        }
        $this->setContentId(Token::generate(8));
        return $this;
    }

    /**
     * @param string|null $contentId
     * @return AttachmentInterface
     */
    public function setContentId($contentId)
    {
        if (null === $this->disposition) {
            $this->setDisposition();
        }
        $this->contentId = $contentId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string|null $disposition
     * @return AttachmentInterface
     */
    public function setDisposition($disposition = AttachmentInterface::DISPOSITION_INLINE)
    {
        $this->disposition = $disposition;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'path' => $this->getPath(),
            'content' => $this->getContent(),
            'name' => $this->getName(),
            'mimeType' => $this->getMimeType(),
            'contentId' => $this->getContentId(),
            'disposition' => $this->getDisposition()
        ];
    }
}
