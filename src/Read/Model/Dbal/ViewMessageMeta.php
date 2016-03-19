<?php

namespace Milio\Message\Read\Model\Dbal;

class ViewMessageMeta
{
    private $messageId;
    private $userId;
    private $isRead = false;

    public function __construct($messageId, $userId)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return boolean
     */
    public function isRead()
    {
        return $this->isRead;
    }

    public function setIsRead($boolean)
    {
        $this->isRead = $boolean;
    }

    public function toArray()
    {
        return [
            'user_id' => $this->getUserId(),
            'message_id' => $this->getMessageId(),
            'is_read' => $this->isRead(),
        ];
    }

    public static function fromArray($data)
    {
        $meta = new self($data['message_id'], $data['user_id']);
        $meta->setIsRead($data['is_read']);

        return $meta;
    }
}
