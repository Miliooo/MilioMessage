<?php

namespace Milio\Message\Read\Model\Dbal;

use Milio\Message\Write\Events\MessageAddedEvent;

class ViewMessage
{
    private $threadId;
    private $messageId;
    private $sender;
    private $body;
    private $createdAt;
    private $messageMeta;

    private function __construct($threadId, $messageId, $sender, $body, \DateTime $createdAt)
    {
        $this->threadId = $threadId;
        $this->messageId = $messageId;
        $this->sender = $sender;
        $this->body = $body;
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param ViewMessageMeta $meta
     */
    public function addMessageMeta(ViewMessageMeta $meta)
    {
        $this->messageMeta[$meta->getUserId()] = $meta;
    }

    /**
     * @return ViewMessageMeta[]
     */
    public function getMessageMeta()
    {
        return $this->messageMeta;
    }

    /**
     * @param $participantId
     * @return ViewMessageMeta
     */
    public function getMessageMetaForParticipant($participantId)
    {
        if (false === array_key_exists($participantId, $this->messageMeta)) {
            throw new \InvalidArgumentException('no message meta found for '.$participantId);
        }

        return $this->messageMeta[$participantId];
    }

    /**
     * Returns a message object with metas from a given array.
     *
     * @param array $array
     *
     * @return ViewMessage
     */
    public static function fromArray($array)
    {
        $metas = [];
        foreach ($array['metas'] as $messageMeta) {
            $metas[] = ViewMessageMeta::fromArray($messageMeta);
        }

        $message = new self(
            $array['thread_id'],
            $array['message_id'],
            $array['sender'],
            $array['body'],
            $array['created_at']
        );

        foreach ($metas as $meta) {
            $message->addMessageMeta($meta);
        }

        return $message;
    }

    public function toArray()
    {
        $metas = [];
        foreach ($this->getMessageMeta() as $singleMeta) {
            $metas[] = $singleMeta->toArray();
        }

        return [
            'thread_id' => $this->getThreadId(),
            'message_id' => $this->getMessageId(),
            'sender' => $this->getSender(),
            'body' => $this->getBody(),
            'created_at' => $this->getCreatedAt(),
            'metas' => $metas,
        ];
    }

    /**
     * Creates a new thread from a message added event.
     *
     * This also updates the thread meta for each user
     *
     * @param MessageAddedEvent $event
     * @param ViewThread $thread
     * @return ViewThread
     */
    public static function createFromEvent(MessageAddedEvent $event, ViewThread $thread)
    {
        if ($event->getThreadId() !== $thread->getThreadId()) {
            throw new \InvalidArgumentException('message does not belong to thread');
        }

        $message = new self(
            $event->getThreadId(),
            $event->getMessageId(),
            $event->getSenderId(),
            $event->getBody(),
            $event->getCreatedAt()
        );

        //create message meta for sender
        $metaSender = new ViewMessageMeta($event->getMessageId(), $event->getSenderId());
        $metaSender->setIsRead(true);

        $message->addMessageMeta($metaSender);

        //update the thread meta for sender
        $tmSender = $thread->getThreadMetaForParticipant($event->getSenderId());
        $tmSender->setLastMessageDate($event->getCreatedAt());

        //create message meta for receiver
        foreach ($thread->getOtherParticipants($event->getSenderId()) as $receiver) {
            $metaReceiver = new ViewMessageMeta($event->getMessageId(), $receiver);
            $metaReceiver->setIsRead(false);
            $message->addMessageMeta($metaReceiver);

            //update thread meta for receiver
            $tmReceiver = $thread->getThreadMetaForParticipant($receiver);
            $tmReceiver->setIsInbox(true);
            $tmReceiver->setUnreadCount($tmReceiver->getUnreadCount() + 1);
            $tmReceiver->setLastMessageDate($event->getCreatedAt());
        }

        $thread->addMessage($message);

        return $thread;
    }
}
