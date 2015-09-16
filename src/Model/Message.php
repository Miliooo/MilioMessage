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
    protected $id;

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
     * A collection of message metas
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
     * @param ThreadInterface $thread
     * @param string $senderId
     * @param string $body
     * @param \DateTime $createdAt
     */
    public function __construct(ThreadInterface $thread, $senderId, $body, \DateTime $createdAt)
    {
        $this->thread = $thread;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->messageMeta = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
        $messageMeta->setMessage($this);
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
}