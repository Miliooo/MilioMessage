<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Message implements MessageInterface
{
    /**
     * The unique id of the message
     *
     * @var integer The unique id of the message
     */
    protected $messageId;

    /**
     * The creation time of the message
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * The sender of the message
     *
     * @var string
     */
    protected $senderId;

    /**
     * The body of the message
     *
     * @var string
     */
    protected $body;

    /**
     * A collection of message meta
     *
     * @var ArrayCollection
     */
    protected $messageMeta;

    /**
     * The thread this message belongs to
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @param MessageId $messageId
     * @param ThreadInterface $thread
     * @param $senderId
     * @param $body
     * @param \DateTime $createdAt
     */
    public function __construct(MessageId $messageId, ThreadInterface $thread, $senderId, $body, \DateTime $createdAt)
    {
        $this->messageId = $messageId;
        $this->thread = $thread;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->messageMeta = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return $this->messageId;
    }


    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * {@inheritdoc}
     */
    public function addMessageMeta(MessageMetaInterface $messageMeta)
    {
        $this->messageMeta->add($messageMeta);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageMeta()
    {
        return $this->messageMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageMetaForParticipant($userId)
    {
        foreach ($this->messageMeta as $meta) {
            if ($meta->getParticipant() === $userId) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getThread()
    {
        return $this->thread;
    }

    public static function createMessageId()
    {
        return new MessageId(MessageId::generate());
    }

    public static function createMessageMetaId()
    {
        return new MessageMetaId(MessageMetaId::generate());
    }

    /**
     * Gets the message class.
     *
     * Overwrite this method if you have a custom message class.
     * This should extend the Message class provided in this library
     *
     * @param MessageId $messageId
     * @param ThreadInterface $thread
     * @param $senderId
     * @param $body
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public static function getMessageClass(MessageId $messageId, ThreadInterface $thread, $senderId, $body, \DateTime $createdAt)
    {
        return new Message($messageId, $thread,  $senderId, $body, $createdAt);
    }

    /**
     * Gets the message meta class
     *
     * Overwrite this method if you have a custom message meta class.
     * This should extend the message meta class provided in this library
     *
     * @param MessageMetaId    $messageMetaId
     * @param MessageInterface $message
     * @param $participant
     *
     * @return MessageMeta
     */
    public static function getMessageMetaClass(MessageMetaId $messageMetaId, MessageInterface $message, $participant)
    {
        return new MessageMeta($messageMetaId, $message, $participant);
    }
}
