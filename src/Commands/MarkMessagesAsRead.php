<?php

namespace Milio\Message\Commands;

use Milio\Message\Model\ThreadId;
use Milio\Message\Model\MessageId;

class MarkMessagesAsRead
{
    protected $threadId;

    /**
     * @var MessageId[]
     */
    protected $messageIds;

    protected $participantId;

    /**
     * @param ThreadId    $threadId
     * @param MessageId[] $messageIds
     * @param string      $participantId
     */
    public function __construct(ThreadId $threadId, $messageIds, $participantId)
    {
        $this->threadId = $threadId;
        $this->messageIds = $messageIds;
        $this->participantId = $participantId;
    }

    /**
     * @return MessageId[]
     */
    public function getMessageIds()
    {
        return $this->messageIds;
    }

    public function getParticipant()
    {
        return $this->participantId;
    }

    public function getThreadId()
    {

    }
}