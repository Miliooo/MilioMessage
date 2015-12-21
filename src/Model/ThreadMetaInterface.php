<?php

namespace Milio\Message\Model;

interface ThreadMetaInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_ARCHIVED = 2;

    /**
     * Gets the unique id of the thread meta
     *
     * @return string
     */
    public function getThreadMetaId();

    /**
     * Gets the participant of the thread meta
     *
     * @return string the participant id
     */
    public function getParticipant();

    /**
     * Gets the thread for this thread meta
     *
     * @return ThreadInterface the thread this meta belongs to
     */
    public function getThread();

    /**
     * Gets the status of the given thread for the participant of the threadMeta.
     *
     * @return integer one of the status constants
     */
    public function getStatus();

    /**
     * Sets the status of the thread for the given participant.
     *
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * Gets the datetime when the participant has written his last message for
     * the given thread
     *
     * @return \DateTime
     */
    public function getLastParticipantMessageDate();

    /**
     * Sets the datetime when the participant has written his last message for the given thread
     *
     * @param \DateTime $dateTime DateTime of participant's last message
     */
    public function setLastParticipantMessageDate(\DateTime $dateTime);

    /**
     * Gets the date time of the last message written by another participant
     *
     * @return \DateTime datetime of the last message written by another participant
     */
    public function getLastMessageDate();

    /**
     * Sets the date of the last message written by another participant
     *
     * @param \DateTime $lastMessageDate datetime of the last message by another participant
     */
    public function setLastMessageDate(\DateTime $lastMessageDate);

    /**
     * Gets the number of unread messages for the participant from the given thread.
     *
     * @return integer The number of unread messages from the thread for the given participant
     */
    public function getUnreadMessageCount();

    /**
     * Sets the number of unread messages for the participant from the given thread.
     *
     * @param integer $unreadCount The number of unread messages from the thread for the given participant
     */
    public function setUnreadMessageCount($unreadCount);
}
