<?php

namespace Milio\Message\Read\Provider\Folder;

use Doctrine\DBAL\Connection;

class InboxThreadProvider
{
    private $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getFolderData($userId)
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
            ->where('tm.user_id = :userId')
            ->setParameter('userId', $userId, \PDO::PARAM_STR)
            ->orderBy('tm.last_message_date', 'DESC')
            ->setMaxResults(10)
        ;

        $stmt = $qb->execute();
        $threadData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $threadData;
    }
}