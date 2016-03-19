<?php

namespace Milio\Message\Read\Projectors;

use Broadway\ReadModel\Projector;
use Doctrine\DBAL\Connection;
use Milio\Message\Read\Model\Dbal\ViewMessage;
use Milio\Message\Write\Events\MessageAdded;
use Milio\Message\Write\Events\MessageAddedEvent;
use Milio\Message\Write\Events\ThreadCreatedEvent;
use Milio\Message\Read\Model\Dbal\ViewThread;

class DbalProjector extends Projector
{
    private $connection;
    private $threadTable;
    private $threadMetaTable;

    public function __construct()
    {
    }


    public function handleThreadCreatedEvent(ThreadCreatedEvent $event)
    {
        $thread = ViewThread::createFromEvent($event);

        /*$this->connection->insert(
            'milio_thread',
            [
                'thread_id' => $thread->getThreadId(),
                'subject' => $thread->getSubject(),
                'sender' => $thread->getSender(),
                'created_at' => $thread->getDateCreated(),
            ]);

        foreach ($thread->getThreadMeta() as $threadMeta) {
            $this->connection->insert(
                $this->threadMetaTable,
                [
                    'thread_id' => $threadMeta->getThreadId(),
                    'user_id' => $threadMeta->getUserId(),
                    'unread_count' => $threadMeta->getUnreadCount(),
                    'is_inbox' => $threadMeta->isInbox(),
                    ''
                ]
            );
        }*/

        return $thread;
    }

    public function handleMessageAddedEvent(MessageAddedEvent $event)
    {
        //get the thread..
        $thread = $this->threadProvider->getThread($event->getThreadId());
        $thread = ViewMessage::createFromEvent($event, $thread);

        return $thread;
    }
}
