<?php

namespace Miliio\Message\Commands;

use Milio\Message\Commands\CreateNewThreadCommand;
use Milio\Message\Model\Thread;
use Milio\Message\Model\ThreadInterface;

class CreateNewThreadCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_a_thread()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertInstanceOf('Milio\Message\Model\ThreadInterface', $thread);
    }

    /**
     * @test
     */
    public function it_returns_the_thread_id()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('thread_id', $thread->getThreadId());
    }

    /**
     * @test
     */
    public function it_returns_the_created_by()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('sender_id', $thread->getCreatedBy());
    }

    /**
     * @test
     */
    public function it_returns_the_creation_at()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);

        $created = new \DateTime('2011-01-01');
        $this->assertEquals($created , $thread->getCreatedAt());
    }

    /**
     * @test
     */
    public function sender_of_new_thread_has_zero_unread_messages()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(0, $this->getSenderThreadMeta($thread)->getUnreadMessageCount());
    }

    /**
     * @test
     */
    public function retriever_of_new_thread_has_one_unread_message()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(1, $this->getReceiverThreadMeta($thread, 'receiver_1')->getUnreadMessageCount());
    }

    /**
     * @test
     */
    public function a_new_thread_has_one_message()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(1, count($thread->getMessages()));
        $this->assertInstanceOf('Milio\Message\Model\MessageInterface', $thread->getMessages()[0]);
    }

    /**
     * @test
     */
    public function sender_of_message_is_owner_of_thread()
    {
        $command = $this->getCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('sender_id', $thread->getMessages()[0]->getSender());

    }

    private function getCommand()
    {
        return new CreateNewThreadCommand('thread_id', 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
    }

    private function getSenderThreadMeta(ThreadInterface $thread)
    {
        return $thread->getThreadMetaForParticipant('sender_id');
    }

    private function getReceiverThreadMeta(ThreadInterface $thread, $userId)
    {
        return $thread->getThreadMetaForParticipant($userId);
    }
}
