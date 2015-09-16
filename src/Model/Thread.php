<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Commands\CreateThread;
use Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException;

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
     * @return ThreadInterface
     */
    public static function createNewThread(CreateThread $command)
    {
        $thread = Thread::getThreadClass($command->getThreadId(), $command->getSenderId(), $command->getCreatedAt());
        $metaSender =  new ThreadMeta($thread, $command->getSenderId());
        $metaSender->setLastParticipantMessageDate($thread->getCreatedAt());
        $metaSender->setUnreadMessageCount(0);
        $thread->addThreadMeta($metaSender);

        foreach($command->getReceiverIds() as $receiverId) {
            $metaReceiver = new ThreadMeta($thread, $receiverId);
            $metaReceiver->setUnreadMessageCount(1);
            $metaReceiver->setLastMessageDate($thread->getCreatedAt());
            $thread->addThreadMeta($metaReceiver);
        }

        $message = Thread::getMessageClass($thread, $command->getSenderId(), $command->getBody(), $command->getCreatedAt());
        $thread->addMessage($message);
        return $thread;
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
    public function getThreadMetaForParticipant($userId)
    {
        foreach ($this->threadMeta as $meta) {
            if ($meta->getParticipant() == $userId) {
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
     * @param string $threadId
     * @param string $createdBy
     * @param \DateTime $createdAt
     *
     * @return Thread
     */
    public static function getThreadClass(ThreadId $threadId, $createdBy, \DateTime $createdAt)
    {
        return new Thread($threadId, $createdBy, $createdAt);
    }

    /**
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

    protected function addThreadMeta(ThreadMetaInterface $threadMeta)
    {
        $this->threadMeta->add($threadMeta);
    }

    protected function addMessage(MessageInterface $message)
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
     * @param ThreadMetaInterface $threadMeta The threadm eta we extract the participant from
     */
    private function addParticipantFromThreadMeta(ThreadMetaInterface $threadMeta)
    {
        $participant = $threadMeta->getParticipant();

        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }
    }
}