<?php

namespace Milio\Message\Read\Projectors;

use Broadway\ReadModel\Projector;
use Milio\Message\Read\Model\Dbal\ViewMessage;
use Milio\Message\Read\Provider\DbalThreadProvider;
use Milio\Message\Read\Saver\DbalThreadSaver;
use Milio\Message\Write\Events\MessageAddedEvent;
use Milio\Message\Write\Events\ThreadCreatedEvent;
use Milio\Message\Read\Model\Dbal\ViewThread;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Projector responsible for generating the thread model and passing it to the dbal storage.
 */
class DbalProjector extends Projector
{
    private $threadProvider;
    private $saver;

    /**
     * DbalProjector constructor.
     *
     * @param DbalThreadProvider $threadProvider
     * @param DbalThreadSaver    $saver
     */
    public function __construct(DbalThreadProvider $threadProvider, DbalThreadSaver $saver)
    {
        $this->threadProvider = $threadProvider;
        $this->saver = $saver;
    }

    public function handleThreadCreatedEvent(ThreadCreatedEvent $event)
    {
        $thread = ViewThread::createFromEvent($event);

        $this->saver->threadCreated($thread);
    }

    public function handleMessageAddedEvent(MessageAddedEvent $event)
    {
        $thread = $this->threadProvider->getThread($event->getThreadId());

        //this can happen if we receive events in the wrong order.
        if (!$thread) {
            return;
        }

        $updated = ViewMessage::createFromEvent($event, $thread);
        $this->saver->messageAdded($updated, array_slice($thread->getMessages(), -1, 1));
    }
}
