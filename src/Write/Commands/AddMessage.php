<?php

namespace Milio\Message\Write\Commands;

class AddMessage
{
    /**
     * @var string
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

    public function __construct($threadId, $senderId, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->senderId = $senderId;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }
}
