<?php

namespace Milio\Message\Events;

//todo not sure how much info we need, maybe just the thread id?
class ThreadCreatedEvent
{
    private $threadId;
    private $senderId;
    private $receiverIds;
    private $title;
    private $message;
    private $createdAt;

    public function __construct($threadId, $senderId, array $receiverIds, $title, $message, $createdAt)
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

    public function getCreatedAtUTC()
    {
        return $this->createdAt;
    }
}