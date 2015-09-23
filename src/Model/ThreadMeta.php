<?php

namespace Milio\Message\Model;


/**
 * The thread meta model
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ThreadMeta implements ThreadMetaInterface
{
    /**
     * The unique id of the thread
     *
     * @var integer
     */
    protected $id;

    /**
     * The thread this meta belongs to
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * The participant of the thread meta
     *
     * @var string
     */
    protected $participant;

    /**
     * The status of the given thread for the participant
     *
     * @var string
     */
    protected $status;

    /**
     * Datetime of the last message written by the participant
     *
     * @var \DateTime
     */
    protected $lastParticipantMessageDate;

    /**
     * Datetime of the last message written by another participant
     *
     * @var \DateTime
     */
    protected $lastMessageDate;

    /**
     * The number of unread messages for the participant for the given thread.
     *
     * @var integer
     */
    protected $unreadMessageCount = 0;

    /**
     * @param ThreadInterface $thread
     * @param $participant
     * @param \DateTime $lastMessageDate
     */
    public function __construct(ThreadInterface $thread, $participant, \DateTime $lastMessageDate)
    {
        $this->status = ThreadMetaInterface::STATUS_ACTIVE;
        $this->thread = $thread;
        $this->participant = $participant;
        $this->lastMessageDate = $lastMessageDate;
    }

    /**
     * Gets the unique id
     *
     * @return integer|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * {@inheritdoc}
     */
    public function setParticipant($participant)
    {
        $this->participant = $participant;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     * @param $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, [ThreadMetaInterface::STATUS_ACTIVE, ThreadMetaInterface::STATUS_ARCHIVED], true)) {
            throw new \InvalidArgumentException('Not a valid status');
        }

        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastParticipantMessageDate(\DateTime $dateTime)
    {
        $this->lastParticipantMessageDate = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastParticipantMessageDate()
    {
        return $this->lastParticipantMessageDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMessageDate()
    {
        return $this->lastMessageDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastMessageDate(\DateTime $lastMessageDate)
    {
        $this->lastMessageDate = $lastMessageDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnreadMessageCount()
    {
        return $this->unreadMessageCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnreadMessageCount($unreadCount)
    {
        $this->unreadMessageCount = intval($unreadCount);
    }
}