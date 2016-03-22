<?php

namespace Milio\Message\Write\Events;

use Broadway\Serializer\SerializableInterface;

class MessageAddedEvent implements SerializableInterface
{
    /**
     * @var
     */
    private $threadId;
    /**
     * @var
     */
    private $messageId;
    /**
     * @var
     */
    private $senderId;
    /**
     * @var
     */
    private $body;
    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct($threadId, $messageId, $senderId, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->messageId = $messageId;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
    }
}
