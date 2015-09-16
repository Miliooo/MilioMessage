<?php

namespace Milio\Message\Commands;

class CreateNewThreadCommand
{
    private $threadId;
    private $senderId;
    private $receiverIds;
    private $title;
    private $message;
    private $createdAt;

    /**
     * @param string $threadId
     * @param string $senderId
     * @param string[] $receiverIds
     * @param string $title
     * @param string $message
     * @param \DateTime $createdAt
     */
    public function __construct($threadId, $senderId, array $receiverIds, $title, $message, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->senderId = $senderId;
        $this->receiverIds = $receiverIds;
        $this->title = $title;
        $this->message = $message;
        $this->createdAt = $createdAt;
    }

    public function getSenderId()
    {
        return $this->senderId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getThreadId()
    {
        return $this->threadId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getReceiverIds()
    {
        return $this->receiverIds;
    }

    public function getMessage()
    {
        return $this->message;
    }
}