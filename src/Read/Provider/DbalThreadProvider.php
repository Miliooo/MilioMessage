<?php

namespace Milio\Message\Read\Provider;

use Doctrine\DBAL\Connection;

class DbalThreadProvider
{
    private $connection;

    /**
     * DbalThreadProvider constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getThread($threadId)
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('
            t.*,
            tm.*
        ')
            ->from('milio_thread', 't')
            ->leftJoin('t', 'milio_thread_meta', 'tm', 'tm.thread_id = t.thread_id')
            ->where('t.thread_id = :threadId')
            ->setParameter('threadId', $threadId, \PDO::PARAM_STR);
        $stmt = $qb->execute();

        $data = $stmt->fetchAll();

        var_dump($data);
    }
}
