<?php

namespace Milio\Message\Read\Provider;

use Doctrine\DBAL\Connection;
use Milio\Message\Read\Model\Dbal\ViewMessage;
use Milio\Message\Read\Model\Dbal\ViewThread;

/**
 * This class is responsible for returning the thread object.
 */
class DbalThreadProvider
{
    private $connection;

    /**
     * DbalThreadProvider constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $threadId
     *
     * @return ViewThread|null
     */
    public function getThread($threadId)
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('
            t.thread_id,
            t.sender,
            t.subject,
            t.created_at,
            tm.user_id as meta_user_id,
            tm.is_inbox as meta_is_inbox,
            tm.unread_count as meta_unread_count,
            tm.last_message_date as meta_last_message_date
        ')
            ->from('milio_thread', 't')
            ->innerJoin('t', 'milio_thread_meta', 'tm', 'tm.thread_id = t.thread_id')
            ->where('t.thread_id = :threadId')
            ->setParameter('threadId', $threadId, \PDO::PARAM_STR);

        $stmt = $qb->execute();
        $threadData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($threadData)) {
            return null;
        }

        $i = 0;
        $threadArr = [];
        foreach ($threadData as $row) {
            if ($i == 0) {
                $createdAt = new \DateTime($row['created_at']);
                $threadArr['thread_id'] = $row['thread_id'];
                $threadArr['sender'] = $row['sender'];
                $threadArr['subject'] = $row['subject'];
                $threadArr['created_at'] = $createdAt->format(\DateTime::ISO8601);
            }
            $lmd = new \DateTime($row['meta_last_message_date']);
            $threadArr['metas'][] =
                [
                    'thread_id' => $row['thread_id'],
                    'user_id' => $row['meta_user_id'],
                    'is_inbox' => $row['meta_is_inbox'],
                    'unread_count' => intval($row['meta_unread_count']),
                    'last_message_date' => $lmd->format(\DateTime::ISO8601),
                ];

            ++$i;
        }
        //creates a thread object but without the messages
        $thread =  ViewThread::fromArray($threadArr);

        $messages = $this->getMessages($threadId);

        foreach($messages as $message) {
            $thread->addMessage($message);
        }

        return $thread;
    }

    /**
     * @param $threadId
     *
     * @return ViewMessage[]
     */
    public function getMessages($threadId)
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('
            m.thread_id,
            m.message_id,
            m.sender,
            m.body,
            m.created_at,
            mm.user_id as meta_user_id,
            mm.is_read as meta_is_read
        ')
            ->from('milio_message', 'm')
            ->leftJoin('m', 'milio_message_meta', 'mm', 'mm.message_id = m.message_id')
            ->where('m.thread_id = :threadId')

            ->setParameter('threadId', $threadId, \PDO::PARAM_STR);
        $stmt = $qb->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($data)) {
            return [];
        }

        $result = [];
        $message_arr = [];
        //make an array with unique message ids...
        foreach ($data as $row) {
            $message_arr[$row['message_id']][] = $row;
        }

        foreach ($message_arr as $messageId => $value) {
            $i = 0;
            foreach ($value as $row) {
                if ($i == 0) {
                    $createdAt = new \DateTime($row['created_at']);
                    $arr['thread_id'] = $row['thread_id'];
                    $arr['message_id'] = $row['message_id'];
                    $arr['sender'] = $row['sender'];
                    $arr['body'] = $row['body'];
                    $arr['created_at'] = $createdAt->format(\DateTime::ISO8601);
                }

                $arr['metas'][] =
                    [
                        'message_id' => $row['message_id'],
                        'user_id' => $row['meta_user_id'],
                        'is_read' => $row['meta_is_read'],
                    ];

                ++$i;
            }

            if ($arr) {
                $result[] = ViewMessage::fromArray($arr);
            }
        }

        return $result;
    }
}
