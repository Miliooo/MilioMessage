<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Commands\CreateNewThreadCommand;

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
     * An array collection of thread metas for this thread
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


    public static function createNewThread(CreateNewThreadCommand $command)
    {
        $thread = new Thread();
        $thread->messages = new ArrayCollection();
        $thread->threadMeta = new ArrayCollection();
        $thread->participants = new ArrayCollection();
        $thread->createdBy = $command->getSenderId();
        $thread->createdAt = $command->getCreatedAt();
        $thread->threadId = $command->getThreadId();

        //create new threadMeta for each participant
        //todo check if we need to set lastMessageDate
        $metaSender =  new ThreadMeta();
        $metaSender->setThread($thread);
        $metaSender->setParticipant($command->getSenderId());
        $metaSender->setLastParticipantMessageDate($thread->createdAt);
        $metaSender->setUnreadMessageCount(0);
        $thread->threadMeta->add($metaSender);

        foreach($command->getReceiverIds() as $receiverId) {
            $metaReceiver = new ThreadMeta();
            $metaReceiver->setThread($thread);
            $metaReceiver->setParticipant($receiverId);
            $metaReceiver->setUnreadMessageCount(1);
            $metaReceiver->setLastMessageDate($thread->createdAt);
            $thread->threadMeta->add($metaReceiver);
        }

        $message = new Message();
        $message->setCreatedAt($command->getCreatedAt());
        $message->setSender($command->getSenderId());
        $message->setBody($command->getMessage());
        $message->setThread($thread);
        $thread->messages->add($message);
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

        throw new \InvalidArgumentException('could not find meta for user id with '.$userId);

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
        return $this->threadId;
    }
}