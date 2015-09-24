<?php

namespace Milio\Message\Commands;

use Milio\Message\Model\ThreadId;

class CreateThread
{
    /**
     * @var ThreadId
     */
    private $threadId;
    private $senderId;
    private $receiverIds;
    private $subject;
    private $body;
    private $createdAt;

    /**
     * @param ThreadId $threadId
     * @param string $senderId
     * @param string[] $receiverIds
     * @param string $subject
     * @param string $body
     * @param \DateTime $createdAt
     */
    public function __construct(ThreadId $threadId, $senderId, array $receiverIds, $subject, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->senderId = $senderId;
        $this->receiverIds = $receiverIds;
        $this->subject = $subject;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @return string Title of the thread
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return ThreadId
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @return \DateTime The date the thread was created
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array|\string[] The receivers of the thread
     */
    public function getReceiverIds()
    {
        return $this->receiverIds;
    }

    /**
     * The body of the message
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}