<?php

namespace Omnimail;

interface AttachmentInterface
{
    const DISPOSITION_INLINE = 'inline';
    const DISPOSITION_ATTACHMENT = 'attachment';
    const DISPOSITION_FORM_DATA = 'form-data';
    const DISPOSITION_SIGNAL = 'signal';
    const DISPOSITION_ALERT = 'alert';
    const DISPOSITION_ICON = 'icon';
    const DISPOSITION_RENDER = 'render';
    const DISPOSITION_RECIPIENT_LIST_HISTORY = 'recipient-list-history';
    const DISPOSITION_SESSION = 'session';
    const DISPOSITION_AIB = 'aib';
    const DISPOSITION_EARLY_SESSION = 'early-session';
    const DISPOSITION_RECIPIENT_LIST = 'recipient-list';
    const DISPOSITION_NOTIFICATION = 'notification';
    const DISPOSITION_BY_REFERENCE = 'by-reference';
    const DISPOSITION_INFO_PACKAGE = 'info-package';
    const DISPOSITION_RECORDING_SESSION = 'recording-session';

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

    /**
     * @return AttachmentInterface
     */
    public function generateContentId();

    /**
     * @param string|null $contentId
     * @return AttachmentInterface
     */
    public function setContentId($contentId);

    /**
     * @return string|null
     */
    public function getContentId();

    /**
     * @param string|null $disposition
     * @return AttachmentInterface
     */
    public function setDisposition($disposition = AttachmentInterface::DISPOSITION_INLINE);

    /**
     * @return string|null
     */
    public function getDisposition();

    /**
     * @return array
     */
    public function toArray();
}
