<?php

namespace Milio\Message\Commands;

use Milio\Message\Model\ThreadId;

class ReplyToThread
{
    private $threadId;
    private $senderId;
    private $body;
    private $createdAt;

    /**
     * @param ThreadId $threadId
     * @param string   $senderId
     * @param string   $body
     * @param \DateTime $createdAt
     */
    public function __construct(ThreadId $threadId, $senderId, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }

    public function getThreadId()
    {
        return $this->threadId;
    }

    public function getSenderId()
    {
        return $this->senderId;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
