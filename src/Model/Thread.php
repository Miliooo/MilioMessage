<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Commands\CreateThread;
use Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException;
use Milio\Message\Commands\ReplyToThread;

class Thread implements ThreadInterface
{
    /**
    * The unique id of the thread
    *
    * @var integer
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

    /**
     * The last message posted in this thread.
     *
     * @var MessageInterface
     */
    protected $lastMessage;

    public function __construct(ThreadId $threadId, $createdBy, $createdAt)
    {
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
        $thread = Thread::getThreadClass($command->getThreadId(), $command->getSenderId(), $command->getCreatedAt());
        $metaSender =  Thread::getThreadMetaClass($thread, $command->getSenderId());
        $metaSender->setLastParticipantMessageDate($thread->getCreatedAt());
        $metaSender->setUnreadMessageCount(0);
        $thread->addThreadMeta($metaSender);

        foreach($command->getReceiverIds() as $receiverId) {
            $metaReceiver = Thread::getThreadMetaClass($thread, $receiverId);
            $metaReceiver->setUnreadMessageCount(1);
            $metaReceiver->setLastMessageDate($thread->getCreatedAt());
            $thread->addThreadMeta($metaReceiver);
        }

        //creates a new message
        self::addMessageToThread($thread, $command->getSenderId(), $command->getReceiverIds(), $command->getBody(), $command->getCreatedAt());

        return $thread;
    }

    public function replyToThread(ReplyToThread $command)
    {
        //although we should validate the command in a service, we still don't want to have an invalid model so we do some
        //assertions here too.
        $this->assertValidThread($command->getThreadId());

        //update the thread meta for the sender.
        $threadMetaSender = $this->getThreadMetaForParticipant($command->getSenderId());
        $threadMetaSender->setLastParticipantMessageDate($command->getCreatedAt());
        $threadMetaSender->setLastMessageDate($command->getCreatedAt());

        //update the thread meta for the receivers.
        $threadMetaReceivers = $this->getThreadMetaNotForParticipant($command->getSenderId());
        foreach($threadMetaReceivers as $threadMetaReceiver) {
            $threadMetaReceiver->setLastMessageDate($command->getCreatedAt());
            $threadMetaReceiver->setUnreadMessageCount($threadMetaReceiver->getUnreadMessageCount() + 1);
        }

        //some logic to get the receivers..
        $receivers = [];
        foreach($this->getParticipants() as $participant) {
            if($participant !== $command->getSenderId()) {
                $receivers[] = $participant;
            }
        }

        self::addMessageToThread($this, $command->getSenderId(), $receivers, $command->getBody(), $command->getCreatedAt());

        return $this;
    }

    protected static function addMessageToThread(ThreadInterface $thread, $senderId, array $receiverIds, $body, $createdAt)
    {
        $message = self::createNewMessage($thread, $senderId, $body, $createdAt);
        self::createMessageMetaSender($message, $senderId);

        foreach($receiverIds as $receiverId) {
            self::createMessageMetaReceiver($message, $receiverId);
        }

        $thread->addMessage($message);
    }

    private static function createNewMessage($thread, $senderId, $body, \DateTime $createdAt)
    {
        $message = Thread::getMessageClass($thread, $senderId, $body, $createdAt);

        return $message;
    }

    private static function createMessageMetaSender(MessageInterface $message, $senderId)
    {
        $messageMeta = Thread::GetMessageMetaClass($message, $senderId);
        $messageMeta->setIsRead(true);
        $message->addMessageMeta($messageMeta);

        return $message;
    }

    private static function createMessageMetaReceiver(MessageInterface $message, $senderId)
    {
        $messageMeta = Thread::GetMessageMetaClass($message, $senderId);
        $messageMeta->setIsRead(false);
        $message->addMessageMeta($messageMeta);

        return $message;
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
     * @param $participantId
     * @return ThreadMetaInterface[]|ArrayCollection[]
     */
    public function getThreadMetaNotForParticipant($participantId)
    {
        $result = [];
        foreach($this->threadMeta as $meta) {
            if($meta->getParticipant() !== $participantId) {
                $result[] = $meta;
            }
        }

        return $result;
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
     * @param ThreadId $threadId
     * @param $createdBy
     * @param \DateTime $createdAt
     * @return Thread
     */
    public static function getThreadClass(ThreadId $threadId, $createdBy, \DateTime $createdAt)
    {
        return new Thread($threadId, $createdBy, $createdAt);
    }

    /**
     * Gets the message class.
     *
     * Overwrite this method if you have a custom message class.
     * This should extend the Message class provided in this library
     *
     * @param ThreadInterface $thread
     * @param $senderId
     * @param $body
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public static function getMessageClass(ThreadInterface $thread, $senderId, $body, \DateTime $createdAt) {
        return new Message($thread,  $senderId, $body, $createdAt);
    }

    /**
     * Gets the thread meta class.
     *
     * Overwrite this method if you have a custom thread meta class.
     * This should extend the thread meta class provided in this library
     *
     * @param ThreadInterface $thread
     * @param $participant
     *
     * @return ThreadMeta
     */
    public static function getThreadMetaClass(ThreadInterface $thread, $participant)
    {
        return new ThreadMeta($thread, $participant);
    }

    /**
     * Gets the message meta class
     *
     * Overwrite this method if you have a custom message meta class.
     * This should extend the message meta class provided in this library
     *
     * @param MessageInterface $message
     * @param $participant
     * @return MessageMeta
     */
    public static function getMessageMetaClass(MessageInterface $message, $participant)
    {
        return new MessageMeta($message, $participant);
    }



    protected function addThreadMeta(ThreadMetaInterface $threadMeta)
    {
        $this->threadMeta->add($threadMeta);
    }

    public function addMessage(MessageInterface $message)
    {
        $this->messages->add($message);
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
    public function isParticipant($userId)
    {
        return $this->getParticipantsCollection()->contains($userId);
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