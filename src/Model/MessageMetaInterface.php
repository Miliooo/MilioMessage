<?php

namespace Milio\Message\Model;

interface MessageMetaInterface
{
    /**
     * @return string the participant identifier
     */
    public function getParticipant();

    /**
     * @return MessageInterface The message this message meta belongs the
     */
    public function getMessage();

    /**
     * Sets the read status
     *
     * @param $boolean
     */
    public function setIsRead($boolean);

    /**
     * @return boolean
     */
    public function isRead();
}
