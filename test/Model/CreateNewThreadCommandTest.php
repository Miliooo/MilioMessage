<?php

namespace Milio\Message\Model;

use Milio\Message\Commands\CreateThread;

class CreateNewThreadCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_a_thread()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertInstanceOf('Milio\Message\Model\ThreadInterface', $thread);
    }

    /**
     * @test
     */
    public function it_returns_the_thread_id()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('thread_id', $thread->getThreadId());
    }

    /**
     * @test
     */
    public function it_returns_the_created_by()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('sender_id', $thread->getCreatedBy());
    }

    /**
     * @test
     */
    public function it_returns_the_creation_at()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);

        $created = new \DateTime('2011-01-01');
        $this->assertEquals($created, $thread->getCreatedAt());
    }

    /**
     * @test
     */
    public function sender_of_new_thread_has_zero_unread_messages()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(0, $this->getSenderThreadMeta($thread)->getUnreadMessageCount());
    }

    /**
     * @test
     */
    public function retriever_of_new_thread_has_one_unread_message()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(1, $this->getReceiverThreadMeta($thread, 'receiver_1')->getUnreadMessageCount());
    }

    /**
     * @test
     */
    public function a_new_thread_has_one_message()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals(1, count($thread->getMessages()));
        $this->assertInstanceOf('Milio\Message\Model\MessageInterface', $thread->getMessages()[0]);
    }

    /**
     * @test
     */
    public function a_message_has_a_body()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('message', $thread->getMessages()[0]->getBody());
    }

    /**
     * @test
     */
    public function sender_of_message_is_owner_of_thread()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $this->assertEquals('sender_id', $thread->getMessages()[0]->getSenderId());
    }

    /**
     * @test
     */
    public function there_are_three_message_metas()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $message = $this->getMessageWhenThreadCreated($thread);
        $this->assertEquals(3, $message->getMessageMeta()->count());
    }

    /**
     * @test
     */
    public function the_sender_has_the_message_set_as_read()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $message = $this->getMessageWhenThreadCreated($thread);
        $this->assertTrue($message->getMessageMetaForParticipant('sender_id')->isRead());
    }

    /**
     * @test
     */
    public function the_receiver_has_the_message_as_unread()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $message = $this->getMessageWhenThreadCreated($thread);
        $this->assertFalse($message->getMessageMetaForParticipant('receiver_1')->isRead());
    }

    /**
     * @test
     * @expectedException \Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException
     */
    public function get_thread_meta_for_non_participant_throws_exception()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $thread->getThreadMetaForParticipant('foo');
    }

    /**
     * @test
     */
    public function there_are_three_participants()
    {
        $command = $this->getCreateThreadCommand();
        $thread = Thread::createNewThread($command);
        $participants = $thread->getParticipants();
        $this->assertEquals(3, count($participants));
        $this->assertTrue($thread->isParticipant('sender_id'));
        $this->assertTrue($thread->isParticipant('receiver_1'));
        $this->assertTrue($thread->isParticipant('receiver_2'));
    }

    private function getCreateThreadCommand()
    {
        return new CreateThread(new ThreadId('thread_id'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
    }

    private function getSenderThreadMeta(ThreadInterface $thread)
    {
        return $thread->getThreadMetaForParticipant('sender_id');
    }

    /**
     * @param ThreadInterface $thread
     * @return MessageInterface
     */
    private function getMessageWhenThreadCreated(ThreadInterface $thread)
    {
        return $thread->getMessages()[0];
    }

    private function getReceiverThreadMeta(ThreadInterface $thread, $userId)
    {
        return $thread->getThreadMetaForParticipant($userId);
    }
}
