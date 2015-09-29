<?php

namespace Milio\Message\Tests\Model;

use Milio\Message\Commands\CreateThread;
use Milio\Message\Commands\ReplyToThread;
use Milio\Message\Model\ThreadId;
use Milio\Message\Model\Thread;
use Milio\Message\Model\ThreadInterface;

class ReplyToThreadCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Thread
     */
    private $thread;

    public function setUp()
    {
        $createCommand = new CreateThread(new ThreadId('thread_id'), 'user_1', ['user_2', 'user_3'], 'this is the title', 'message', $this->getDateCreated());
        $this->thread = Thread::createNewThread($createCommand);
    }

    /**
     * @test
     */
    public function it_returns_a_thread()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $this->assertInstanceOf('Milio\Message\Model\ThreadInterface', $thread);
    }

    /**
     * @test
     */
    public function it_updates_the_last_message_date()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $this->assertEquals($this->getDateCreated(), $thread->getThreadMetaForParticipant('user_2')->getLastMessageDate());
        $this->assertEquals($this->getDateReplied(), $thread->getThreadMetaForParticipant('user_1')->getLastMessageDate());
        $this->assertEquals($this->getDateReplied(), $thread->getThreadMetaForParticipant('user_3')->getLastMessageDate());
    }

    /**
     * @test
     */
    public function it_updates_the_last_participants_message_date()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $this->assertEquals($this->getDateCreated(), $thread->getThreadMetaForParticipant('user_1')->getLastParticipantMessageDate());
        $this->assertEquals($this->getDateReplied(), $thread->getThreadMetaForParticipant('user_2')->getLastParticipantMessageDate());
        $this->assertNull($thread->getThreadMetaForParticipant('user_3')->getLastParticipantMessageDate());
    }

    /**
     * @test
     */
    public function it_updates_the_unread_message_count_in_thread_meta()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        //user 1 created the thread, so he starts at zero, but received a new message from user_2 so now he has 1
        $this->assertEquals(1, $thread->getThreadMetaForParticipant('user_1')->getUnreadMessageCount());
        //user_2 replied to the thread, but has never read the thread, so his unread message count should be 1
        $this->assertEquals(1, $thread->getThreadMetaForParticipant('user_2')->getUnreadMessageCount());
        //user_3 has received two messages but never has seen the thread so his count should be 2
        $this->assertEquals(2, $thread->getThreadMetaForParticipant('user_3')->getUnreadMessageCount());
    }

    /**
     * @test
     */
    public function there_are_two_messages_in_the_thread()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $this->assertEquals(2, $thread->getMessages()->count());
    }

    /**
     * @test
     */
    public function it_updates_the_unread_message_in_message_meta()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $reply = $this->getReply($thread);

        //the sender of the reply has his message as read
        $this->assertTrue($reply->getMessageMetaForParticipant('user_2')->isRead());
        //the receivers have their message as unread
        $this->assertFalse($reply->getMessageMetaForParticipant('user_1')->isRead());
        $this->assertFalse($reply->getMessageMetaForParticipant('user_3')->isRead());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function you_can_not_reply_to_another_thread()
    {
        $replyCommand = new ReplyToThread(new ThreadId('another_thread'), 'user_2', 'hello there', $this->getDateReplied());
        $this->thread->replyToThread($replyCommand);
    }

    /**
     * @test
     */
    public function the_last_message_has_the_right_values()
    {
        $thread = $this->thread->replyToThread($this->getReplyToThreadCommand());
        $reply = $this->getReply($thread);

        $this->assertEquals('hello there', $reply->getBody());
        $this->assertEquals($this->getDateReplied(), $reply->getCreatedAt());
        $this->assertEquals('user_2', $reply->getSenderId());
    }

    private function getReplyToThreadCommand()
    {
        return new ReplyToThread(new ThreadId('thread_id'), 'user_2', 'hello there', $this->getDateReplied());
    }

    private function getDateCreated()
    {
        return new \DateTime('2011-01-01');
    }

    private function getDateReplied()
    {
        return new \DateTime('2012-01-01');
    }

    /**
     * @param ThreadInterface $thread
     *
     * @return MessageInterface
     */
    private function getReply(ThreadInterface $thread)
    {
        $reply = $thread->getMessages()->last();

        return $reply;
    }
}