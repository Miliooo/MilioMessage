<?php

namespace Milio\Message\Read\Model\Dbal;

class ViewThreadMeta
{
    protected $threadId;
    protected $userId;
    protected $isInbox = false;
    protected $unreadCount = 0;
    protected $lastMessageDate;

    public function __construct($threadId, $userId)
    {
        $this->threadId = $threadId;
        $this->userId = (string) $userId;
    }

    /**
     * @return mixed
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @return \DateTime
     */
    public function getLastMessageDate()
    {
        return $this->lastMessageDate;
    }

    /**
     * @param mixed $lastMessageDate
     */
    public function setLastMessageDate(\DateTime $lastMessageDate)
    {
        $this->lastMessageDate = $lastMessageDate;
    }

    /**
     * @return boolean
     */
    public function isInbox()
    {
        return $this->isInbox;
    }

    /**
     * @param boolean $isInbox
     */
    public function setIsInbox($isInbox)
    {
        $this->isInbox = $isInbox;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getUnreadCount()
    {
        return $this->unreadCount;
    }

    /**
     * @param int $unreadCount
     */
    public function setUnreadCount($unreadCount)
    {
        $this->unreadCount = $unreadCount;
    }

    public function toArray()
    {
        return [
            'thread_id' => $this->getThreadId(),
            'user_id' => $this->getUserId(),
            'is_inbox' => $this->isInbox(),
            'unread_count' => $this->getUnreadCount(),
            'last_message_date' => $this->getLastMessageDate()->format(\DateTime::ISO8601)
        ];
    }

    public static function fromArray($array)
    {
        $threadMeta = new self(
            $array['thread_id'],
            $array['user_id']
        );

        $threadMeta->setIsInbox($array['is_inbox']);
        $threadMeta->setUnreadCount($array['unread_count']);
        $threadMeta->setLastMessageDate(
            \DateTime::createFromFormat(\DateTime::ISO8601, $array['last_message_date'])
            );

        return $threadMeta;
    }
}
