<?php

namespace Milio\Message\Model;


class MessageMeta implements MessageMetaInterface
{
    /**
     * @var MessageInterface The message this message meta belongs to
     */
    protected $message;

    /**
     * @var string The participant this message meta belongs to
     */
    protected $participant;

    /**
     * @var boolean The read status
     */
    protected $isRead;

    /**
     * @param MessageInterface $message
     * @param $participant
     */
    public function __construct(MessageInterface $message, $participant)
    {
        $this->message = $message;
        $this->participant = $participant;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRead($boolean)
    {
        $this->isRead = $boolean;
    }

    public function isRead()
    {
        return $this->isRead;
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }
}