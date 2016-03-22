<?php

namespace Milio\Message\Read\Deleter;

use Doctrine\DBAL\Connection;
use Milio\Message\Read\Provider\DbalThreadProvider;

class DbalThreadDeleter
{
    /**
     * @var DbalThreadProvider
     */
    private $threadProvider;

    private $connection;

    public function __construct(Connection $connection, DbalThreadProvider $threadProvider)
    {
        $this->connection = $connection;
        $this->threadProvider = $threadProvider;
    }

    public function deleteThread($threadId)
    {
        $thread = $this->threadProvider->getThread($threadId);

        if($thread) {
            $this->connection->delete('milio_thread', ['thread_id' => $thread->getThreadId()]);
            $this->connection->delete('milio_thread_meta', ['thread_id' => $thread->getThreadId()]);
            foreach ($thread->getMessages() as $message) {
                $messageId = $message->getMessageId();
                $this->connection->delete('milio_message', ['thread_id' => $thread->getThreadId(), 'message_id' => $messageId]);
                $this->connection->delete('milio_message_meta', ['message_id' => $messageId]);
            }
        }
    }
}