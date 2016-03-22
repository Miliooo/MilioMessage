<?php

namespace Milio\Message\Read\Saver;

use Doctrine\DBAL\Connection;
use Milio\Message\Read\Model\Dbal\ViewMessage;
use Milio\Message\Read\Model\Dbal\ViewMessageMeta;
use Milio\Message\Read\Model\Dbal\ViewThread;
use Milio\Message\Read\Model\Dbal\ViewThreadMeta;

/**
 * This class is responsible for storing the correct database representation for the following events.
 *
 * - thread created event
 * - message added event
 */
class DbalThreadSaver
{
    private $connection;

    /**
     * DbalThreadSaver constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Creates a new thread.
     *
     * All operations are insert operations.
     *
     * @param ViewThread $thread
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function threadCreated(ViewThread $thread)
    {
        $this->connection->beginTransaction();
        try {
            $this->doThreadCreated($thread);
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
        $this->connection->close();
    }

    /**
     * Adds a new message to a thread.
     *
     *
     * @param ViewThread    $thread   The thread with all info updated,
     *                                eg the thread meta has been updated for the added messages
     * @param ViewMessage[] $messages
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function messageAdded(ViewThread $thread, array $messages)
    {
        $this->connection->beginTransaction();
        try {
            $this->doMessageAdded($messages);
            $this->doMessageAddedForThread($thread);
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
        $this->connection->close();
    }

    /**
     * @param ViewThread $thread
     */
    protected function doThreadCreated(ViewThread $thread)
    {
        $this->createThread($thread);

        foreach ($thread->getMessages() as $message) {
            $this->saveNewMessage($message);
        }
    }

    /**
     * @param array $messages
     */
    protected function doMessageAdded(array $messages)
    {
        foreach ($messages as $message) {
            $this->saveNewMessage($message);
        }
    }

    /**
     * Do updates to the thread or thread meta when a message added event has happened.
     *
     * @param ViewThread $thread
     */
    private function doMessageAddedForThread(ViewThread $thread)
    {
        foreach ($thread->getThreadMeta() as $meta) {
            $this->connection->update(
                'milio_thread_meta',
                [
                    'is_inbox' => $meta->isInbox(),
                    'unread_count' => intval($meta->getUnreadCount()),
                    'last_message_date' => $meta->getLastMessageDate(),
                ],
                [
                    'thread_id' => $meta->getThreadId(),
                    'user_id' => $meta->getUserId()
                ],
                [
                    \PDO::PARAM_BOOL, //is_inbox
                    \PDO::PARAM_INT, //unread_count
                    'datetime',  //last_message_date
                    \PDO::PARAM_STR, //thread_id
                    \PDO::PARAM_STR //user_id
                ]
            );
        }
    }

    private function saveNewMessage(ViewMessage $message)
    {
        $data = $this->getInsertMessage($message);
        $this->connection->insert('milio_message', $data[0], $data[1]);

        foreach ($message->getMessageMeta() as $meta) {
            $this->connection->insert('milio_message_meta', $this->getInsertMessageMeta($meta), $this->getInsertMessageMetaType());
        }
    }

    /**
     * @param ViewThread $thread
     */
    private function createThread(ViewThread $thread)
    {
        $threadData = $this->getInsertThread($thread);
        $this->connection->insert('milio_thread', $threadData[0], $threadData[1]);
        foreach ($thread->getThreadMeta() as $meta) {
            $metaData = $this->getInsertThreadMeta($meta);
            $this->connection->insert('milio_thread_meta', $metaData[0], $metaData[1]);
        }
    }

    /**
     * @param ViewMessage $message
     *
     * @return array
     */
    protected function getInsertMessage(ViewMessage $message)
    {
        return
            [
                [
                    'thread_id' => $message->getThreadId(),
                    'message_id' => $message->getMessageId(),
                    'sender' => $message->getSender(),
                    'body' => $message->getBody(),
                    'created_at' => $message->getCreatedAt(),
                ],
                [
                    \PDO::PARAM_STR, //thread_id
                    \PDO::PARAM_STR, //message_id
                    \PDO::PARAM_STR, //sender
                    \PDO::PARAM_STR, //body
                    'datetime', //created_at
                ],
            ];
    }

    /**
     * @param ViewMessageMeta $meta
     *
     * @return array
     */
    protected function getInsertMessageMeta(ViewMessageMeta $meta)
    {
        return [
            'message_id' => $meta->getMessageId(),
            'user_id' => $meta->getUserId(),
            'is_read' => $meta->isRead(),
        ];
    }

    /**
     * @return array
     */
    private function getInsertMessageMetaType()
    {
        return [
            \PDO::PARAM_STR, //message_id
            \PDO::PARAM_STR, //user_id
            \PDO::PARAM_BOOL, //is_read
        ];
    }

    /**
     * @param ViewThread $thread
     *
     * @return array
     */
    protected function getInsertThread(ViewThread $thread)
    {
        return
            [
                [
                    'thread_id' => $thread->getThreadId(),
                    'sender' => $thread->getSender(),
                    'subject' => $thread->getSubject(),
                    'created_at' => $thread->getDateCreated(),
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    'datetime',
                ],
            ];
    }

    protected function getInsertThreadMeta(ViewThreadMeta $meta)
    {
        return
            [
                [
                    'thread_id' => $meta->getThreadId(),
                    'user_id' => $meta->getUserId(),
                    'is_inbox' => $meta->isInbox(),
                    'unread_count' => $meta->getUnreadCount(),
                    'last_message_date' => $meta->getLastMessageDate(),
                ],
                [
                    \PDO::PARAM_STR, //thread_id
                    \PDO::PARAM_STR, //user_id
                    \PDO::PARAM_BOOL, //is_inbox
                    \PDO::PARAM_INT, //unread_count
                    'datetime', //last_message_date
                ],
            ];
    }
}
