<?php

namespace Milio\Message\Write\Events;

use Broadway\Serializer\SerializableInterface;

class ThreadCreatedEvent implements SerializableInterface
{
    /**
     * @var string
     */
    private $threadId;
    /**
     * @var string
     */
    private $sender;
    /**
     * @var array
     */
    private $receiverIds;
    /**
     * @var \DateTime
     */
    private $createdAt;

    private $subject;

    public function __construct($threadId, $sender, array $receiverIds, $subject, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->sender = $sender;
        $this->receiverIds = $receiverIds;
        $this->subject = $subject;
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
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return array
     */
    public function getReceiverIds()
    {
        return $this->receiverIds;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['thread_id'],
            $data['sender'],
            $data['receiver_ids'],
            $data['subject'],
            \DateTime::createFromFormat(\DateTime::ISO8601, $data['date_created'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return [
            'thread_id' => $this->getThreadId(),
            'sender' => $this->getSender(),
            'receiver_ids' => $this->getReceiverIds(),
            'subject' => $this->getSender(),
            'date_created' => $this->getCreatedAt()->format(\DateTime::ISO8601)
        ];
    }
}
