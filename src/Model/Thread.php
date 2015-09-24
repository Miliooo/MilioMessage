<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Commands\CreateThread;
use Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException;
use Milio\Message\Commands\ReplyToThread;
use Milio\Message\Commands\MarkMessagesAsRead;

class Thread implements ThreadInterface
{
    /**
     * @var ThreadId
     */
    protected $threadId;

    /**
     * The subject of the thread
     *
     * @var string
     */
    protected $subject;

    /**
     * The participant who created the thread
     *
     * @var string
     */
    protected $createdBy;

    /**
     * The datetime when the thread was created
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * An array collection of messages for this thread
     *
     * @var ArrayCollection|MessageInterface[]
     */
    protected $messages;

    /**
     * An array collection of thread meta for this thread
     *
     * @var ArrayCollection|ThreadMeta[]
     */
    protected $threadMeta;

    /**
     * An array collection with participants
     *
     * @var ArrayCollection
     */
    protected $participants;

    private function __construct(ThreadId $threadId, $subject, $createdBy, $createdAt)
    {
        $this->subject = $subject;
        $this->threadId = $threadId;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
        $this->messages = new ArrayCollection();
        $this->threadMeta = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /**
     * Creates a new thread.
     *
     * @param CreateThread $command
     *
     * @return ThreadInterface
     */
    public static function createNewThread(CreateThread $command)
    {
        $thread = Thread::getThreadClass($command->getThreadId(), $command->getSubject(), $command->getSenderId(), $command->getCreatedAt());
        $metaSender =  Thread::getThreadMetaClass(self::createThreadMetaId(), $thread, $command->getSenderId());
        $metaSender->setLastParticipantMessageDate($command->getCreatedAt());
        $metaSender->setUnreadMessageCount(0);
        $thread->addThreadMeta($metaSender);

        foreach($command->getReceiverIds() as $receiverId) {
            $metaReceiver = Thread::getThreadMetaClass(self::createThreadMetaId(), $thread, $receiverId);
            $metaReceiver->setUnreadMessageCount(1);
            $metaReceiver->setLastMessageDate($command->getCreatedAt());
            $thread->addThreadMeta($metaReceiver);
        }

        self::addMessageToThread($thread, $command->getSenderId(), $command->getReceiverIds(), $command->getBody(), $command->getCreatedAt());

        return $thread;
    }

    public function replyToThread(ReplyToThread $command)
    {
        $this->assertValidThread($command->getThreadId());

        //update the thread meta for the sender.
        $threadMetaSender = $this->getThreadMetaForParticipant($command->getSenderId());
        $threadMetaSender->setLastParticipantMessageDate($command->getCreatedAt());

        $receivers = $this->getOtherParticipants($command->getSenderId());

        foreach($receivers as $receiver) {
            $threadMetaReceiver = $this->getThreadMetaForParticipant($receiver);
            $threadMetaReceiver->setLastMessageDate($command->getCreatedAt());
            $threadMetaReceiver->setUnreadMessageCount($threadMetaReceiver->getUnreadMessageCount() + 1);
        }

        self::addMessageToThread($this, $command->getSenderId(), $receivers, $command->getBody(), $command->getCreatedAt());

        return $this;
    }

    /**
     * @param MarkMessagesAsRead $command
     *
     * @return array Where first key is the thread, and second key is the updated messages
     */
    public function markMessagesAsRead(MarkMessagesAsRead $command)
    {
        if(!$this->isParticipant($command->getParticipant())) {
            return [$this, []];
        }

        $updatedMessages = [];
        foreach($this->getMessages() as $message) {
            if(in_array($message->getId(), $command->getMessageIds(), true)) {
                $messageMetaParticipant = $message->getMessageMetaForParticipant($command->getParticipant());
                $messageMetaParticipant->setIsRead(true);
                $threadMetaParticipant = $this->getThreadMetaForParticipant($command->getParticipant());
                $threadMetaParticipant->setUnreadMessageCount($threadMetaParticipant->getUnreadMessageCount() - 1);
                $updatedMessages[] = $message;
            }
        }

        return [$this, $updatedMessages];
    }

    protected static function addMessageToThread(ThreadInterface $thread, $senderId, array $receiverIds, $body, $createdAt)
    {
        $message = self::createNewMessage(Message::createMessageId(), $thread, $senderId, $body, $createdAt);
        self::createMessageMetaSender(Message::createMessageMetaId(), $message, $senderId);

        foreach($receiverIds as $receiverId) {
            self::createMessageMetaReceiver(Message::createMessageMetaId(), $message, $receiverId);
        }

        $thread->addMessage($message);
    }

    private static function createNewMessage(MessageId $messageId, $thread, $senderId, $body, \DateTime $createdAt)
    {
        $message = Message::getMessageClass($messageId, $thread, $senderId, $body, $createdAt);

        return $message;
    }

    private static function createMessageMetaSender(MessageMetaId $messageMetaId, MessageInterface $message, $senderId)
    {
        $messageMeta = Message::GetMessageMetaClass($messageMetaId, $message, $senderId);
        $messageMeta->setIsRead(true);
        $message->addMessageMeta($messageMeta);
    }

    private static function createMessageMetaReceiver(MessageMetaId $messageMetaId, MessageInterface $message, $senderId)
    {
        $messageMeta = Message::GetMessageMetaClass($messageMetaId, $message, $senderId);
        $messageMeta->setIsRead(false);
        $message->addMessageMeta($messageMeta);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadMeta()
    {
        return $this->threadMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadMetaForParticipant($participantId)
    {
        foreach ($this->threadMeta as $meta) {
            if ($meta->getParticipant() == $participantId) {
                return $meta;
            }
        }

        throw new ThreadMetaForParticipantNotFoundException();
    }


    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadId()
    {
        return $this->threadId->getValue();
    }

    /**
     * Gets the thread class.
     *
     * Overwrite this method if you have a custom thread class.
     * This should extend the thread class provided in this library
     *
     * @param ThreadId $threadId
     * @param string   $subject
     * @param $createdBy
     * @param \DateTime $createdAt
     *
     * @return Thread
     */
    public static function getThreadClass(ThreadId $threadId, $subject, $createdBy, \DateTime $createdAt)
    {
        return new Thread($threadId, $subject, $createdBy, $createdAt);
    }

    /**
     * Gets the thread meta class.
     *
     * Overwrite this method if you have a custom thread meta class.
     * This should extend the thread meta class provided in this library
     *
     * @param ThreadMetaId    $threadMetaId
     * @param ThreadInterface $thread
     * @param string          $participant
     *
     * @return ThreadMeta
     */
    public static function getThreadMetaClass(ThreadMetaId $threadMetaId, ThreadInterface $thread, $participant)
    {
        return new ThreadMeta($threadMetaId, $thread, $participant);
    }

    protected function addThreadMeta(ThreadMetaInterface $threadMeta)
    {
        $this->threadMeta->add($threadMeta);
    }

    public function addMessage(MessageInterface $message)
    {
        $this->messages->add($message);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public static function createThreadId()
    {
        return new ThreadId(ThreadId::generate());
    }

    public static function createThreadMetaId()
    {
        return new ThreadMetaId(ThreadMetaId::generate());
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipants()
    {
        return $this->getParticipantsCollection()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function isParticipant($participantId)
    {
        return $this->getParticipantsCollection()->contains($participantId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOtherParticipants($participantId)
    {
        $otherParticipants = $this->getParticipants();
        $key = array_search($participantId, $otherParticipants, true);
        if (false !== $key) {
            unset($otherParticipants[$key]);
        }
        return array_values($otherParticipants);
    }

    /**
     * Returns an array collection of participants for the given thread.
     *
     * @return ArrayCollection
     */
    private function getParticipantsCollection()
    {
        //doctrine skips constructor and does not make an array collection
        //since there is no mapping taking place
        if ($this->participants == null) {
            $this->participants = new ArrayCollection();
        }

        //there is thread meta in the collection so let's loop over it
        foreach ($this->threadMeta as $threadMeta) {
            $this->addParticipantFromThreadMeta($threadMeta);
        }
        return $this->participants;
    }

    /**
     * Adds a participant form the thread meta
     *
     * @param ThreadMetaInterface $threadMeta The thread meta we extract the participant from
     */
    private function addParticipantFromThreadMeta(ThreadMetaInterface $threadMeta)
    {
        $participant = $threadMeta->getParticipant();

        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }
    }

    private function assertValidThread(ThreadId $threadId)
    {
        if($this->threadId->getValue() !== $threadId->getValue()) {
            throw new \InvalidArgumentException('expected thread to be '.$this->threadId->getValue().' got '.$threadId->getValue());
        }
    }
}