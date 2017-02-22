<?php

namespace Milio\Message\Write\Model;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Milio\Message\Write\Commands\CreateThread;
use Milio\Message\Write\Events\ThreadCreatedEvent;

/**
 * The write thread model.
 *
 * Since we do the projections in the view layer,
 * we do not care about how this should be represented in the database.
 */
class Thread extends EventSourcedAggregateRoot
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
    private $receivers;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    public static function createThread(CreateThread $command)
    {
        $thread = new self();
        $thread->apply(new ThreadCreatedEvent(
            $command->getThreadId(),
            $command->getSenderId(),
            $command->getReceiverIds(),
            $command->getSubject(),
            $command->getCreatedAt()
        ));


        return $thread;
    }

    protected function applyThreadCreatedEvent(ThreadCreatedEvent $event)
    {
        $this->threadId = $event->getThreadId();
        $this->sender = $event->getSender();
        $this->receivers = $event->getReceiverIds();
        $this->subject = $event->getSubject();
        $this->dateCreated = $event->getCreatedAt();
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->threadId;
    }
}
