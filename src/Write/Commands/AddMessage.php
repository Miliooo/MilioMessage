<?php

namespace Milio\Message\Write\Commands;

use Milio\Message\Model\ThreadId;

class AddMessage
{
    /**
     * @var ThreadId
     */
    private $threadId;

    /**
     * @var string
     */
    private $senderId;

    /**
     * @var string
     */
    private $body;

    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct(ThreadId $threadId, $senderId, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }
}
