<?php

namespace Milio\Message\Repository;

use Milio\Message\Model\ThreadInterface;
use Milio\Message\Model\ThreadId;

interface ThreadRepositoryInterface
{
    public function save(ThreadInterface $thread);

    /**
     * @param ThreadId $threadId
     * @return ThreadInterface|null
     */
    public function find(ThreadId $threadId);

    /**
     * @param $participant
     * @return mixed
     */
    public function getInboxThreadsForParticipant($participant);

    /**
     * @param $participant
     * @return mixed
     */
    public function getUnreadMessageCountForParticipant($participant);
}