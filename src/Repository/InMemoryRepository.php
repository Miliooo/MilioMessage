<?php

namespace Milio\Message\Repository;

use Milio\Message\Model\ThreadInterface;
use Milio\Message\Model\ThreadId;
use Milio\Message\Model\ThreadMetaInterface;

class InMemoryRepository implements ThreadRepositoryInterface
{
    /**
     * @var ThreadInterface[]
     */
    private $data = array();

    public function save(ThreadInterface $thread)
    {
        $this->data[$thread->getThreadId()] = $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function find(ThreadId $threadId)
    {
        $threadId = (string) $threadId;

        if (isset($this->data[$threadId])) {
            return $this->data[$threadId];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getInboxThreadsForParticipant($participant)
    {
        $result = [];
        foreach ($this->data as $single) {
            if($single->isParticipant($participant)
                && $single->getThreadMetaForParticipant($participant)->getLastMessageDate() !== null
            && $single->getThreadMetaForParticipant($participant)->getStatus() === ThreadMetaInterface::STATUS_ACTIVE
            ) {
                $result[] = $single;
            }
        }

        return empty($result) ? null : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnreadMessageCountForParticipant($participant)
    {
        $threadsToInspect = $this->getThreadsForParticipant($participant);
        $count = 0;
        foreach($threadsToInspect as $thread) {
            $addValue = (integer) $thread->getThreadMetaForParticipant($participant)->getUnreadMessageCount();
            $count = $count + $addValue;
        }

        return $count;
    }

    /**
     * @param $participant
     *
     * @return ThreadInterface[]|[]
     */
    private function getThreadsForParticipant($participant)
    {
        $results = [];
        foreach($this->data as $thread) {
            if($thread->isParticipant($participant)) {
                $results[] = $thread;
            }
        }

        return $results;
    }
}