<?php

namespace Milio\Message\Read\Model\Dbal;

use Milio\Message\Write\Events\ThreadCreatedEvent;

/**
 * Implementation of a DBAL view thread model.
 *
 * It uses thread meta for easier querying.
 */
class ViewThread
{
    /**
     * @var string
     */
    protected $threadId;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var array
     */
    protected $receivers;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var \DateTime
     */
    protected $dateCreated;

    protected $threadMeta = [];

    protected $messages = [];

    private function __construct($threadId, $sender, array $receivers, $subject, \DateTime $dateCreated)
    {
        $this->threadId = $threadId;
        $this->sender = $sender;
        $this->receivers = $receivers;
        $this->subject = $subject;
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return string
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    public function getReceivers()
    {
        return $this->receivers;
    }

    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return ViewThreadMeta[]
     */
    public function getThreadMeta()
    {
        return $this->threadMeta;
    }

    public function addMessage(ViewMessage $message)
    {
        $this->messages[] = $message;
    }

    /**
     * @return ViewMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param $participantId
     *
     * @return ViewThreadMeta
     */
    public function getThreadMetaForParticipant($participantId)
    {
        if (false === array_key_exists($participantId, $this->threadMeta)) {
            throw new \InvalidArgumentException('no thread meta found for '.$participantId);
        }

        return $this->threadMeta[$participantId];
    }

    /**
     * @return string[]
     */
    public function getParticipants()
    {
        return array_merge([$this->getSender()], $this->getReceivers());
    }

    /**
     * @param string $userId
     *
     * @return array
     */
    public function getOtherParticipants($userId)
    {
        $others = [];
        foreach ($this->getParticipants() as $participant) {
            if ($participant !== $userId) {
                $others[] = $participant;
            }
        }

        return $others;
    }

    public function addThreadMeta(ViewThreadMeta $meta)
    {
        $this->threadMeta[$meta->getUserId()] = $meta;
    }

    public static function fromArray($threadArray)
    {
        $threadMetas = [];
        //since we need the thread metas for getting the receivers, we do it first
        foreach ($threadArray['metas'] as $threadMeta) {
            $threadMetas[] = ViewThreadMeta::fromArray($threadMeta);
        }

        $thread = new self(
            $threadArray['thread_id'],
            $threadArray['sender'],
            self::getReceiversFromMetas($threadMetas, $threadArray['sender']),
            $threadArray['subject'],
            \DateTime::createFromFormat(\DateTime::ISO8601,  $threadArray['created_at'], new \DateTimeZone('UTC'))
            );

        foreach ($threadMetas as $threadMeta) {
            $thread->addThreadMeta($threadMeta);
        }

        return $thread;
    }

    /**
     * Returns an array representation of an thread.
     *
     * @return array
     */
    public function toArray()
    {
        $metas = [];
        foreach ($this->getThreadMeta() as $singleMeta) {
            $metas[] = $singleMeta->toArray();
        }

        $messages = [];
        foreach ($this->getMessages() as $message) {
            $messages[] = $message->toArray();
        }

        return [
            'thread_id' => $this->getThreadId(),
            'sender' => $this->getSender(),
            'receivers' => $this->getReceivers(),
            'subject' => $this->getSubject(),
            'created_at' => $this->getDateCreated()->format(\DateTime::ISO8601),
            'metas' => $metas,
            'messages' => $messages,
        ];
    }

    /**
     * Creates a thread from a thread created event.
     *
     * @param ThreadCreatedEvent $event
     *
     * @return ViewThread
     */
    public static function createFromEvent(ThreadCreatedEvent $event)
    {
        $thread = new self(
            $event->getThreadId(),
            $event->getSender(),
            $event->getReceiverIds(),
            $event->getSubject(),
            $event->getCreatedAt()
        );

        $threadMeta = self::createThreadMeta($event->getThreadId(), $event->getSender());
        $threadMeta->setLastMessageDate($event->getCreatedAt());
        $thread->addThreadMeta($threadMeta);

        foreach ($event->getReceiverIds() as $receiver) {
            $threadMeta = self::createThreadMeta($event->getThreadId(), $receiver);
            $threadMeta->setLastMessageDate($event->getCreatedAt());
            $thread->addThreadMeta($threadMeta);
        }

        return $thread;
    }

    /**
     * @param string $threadId
     * @param string $userId
     *
     * @return ViewThreadMeta
     */
    protected static function createThreadMeta($threadId, $userId)
    {
        $threadMeta = new ViewThreadMeta($threadId, $userId);

        return $threadMeta;
    }

    /**
     * Since we do not store the receivers in the thread table, we get them from the metas.
     *
     * @param ViewThreadMeta[] $threadMetas
     * @param string           $sender
     *
     * @return string[]
     */
    protected static function getReceiversFromMetas($threadMetas, $sender)
    {
        $receivers = [];
        foreach ($threadMetas as $meta) {
            if ($sender !== $meta->getUserId()) {
                $receivers[] = $meta->getUserId();
            }
        }

        return $receivers;
    }
}
