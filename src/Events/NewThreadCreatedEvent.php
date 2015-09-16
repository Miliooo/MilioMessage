<?php

namespace Milio\Message\Events;

use Broadway\Serializer\SerializableInterface;
use BroadwaySerialization\Serialization\Serializable;

class NewThreadCreatedEvent implements SerializableInterface
{
    use Serializable;

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