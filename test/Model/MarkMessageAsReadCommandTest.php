<?php

namespace Milio\Message\Tests\Model;

use Milio\Message\Commands\CreateThread;
use Milio\Message\Commands\MarkMessagesAsRead;
use Milio\Message\Model\Thread;
use Milio\Message\Model\ThreadId;

class MarkMessageAsReadCommandTest extends AbstractThreadCommand
{
    /**
     * @test
     */
    public function test_it()
    {
        $threadCommand = $this->getCreateThreadCommand();

        /** @var Thread $thread */
        $thread = Thread::createNewThread($threadCommand);
        $messages = $thread->getMessages();

        //it was unread before
        $this->assertEquals(false, $thread->getMessages()[0]->getMessageMetaForParticipant('receiver_1')->isRead());
        //one unread count
        $this->assertEquals(1, $thread->getThreadMetaForParticipant('receiver_1')->getUnreadMessageCount());
        $thread->markMessagesAsRead(new MarkMessagesAsRead(new ThreadId($thread->getThreadId()), [$messages[0]->getMessageId()], 'receiver_1'));

        //now it's read
        $this->assertEquals(true, $thread->getMessages()[0]->getMessageMetaForParticipant('receiver_1')->isRead());
        $this->assertEquals(0, $thread->getThreadMetaForParticipant('receiver_1')->getUnreadMessageCount());

        $this->assertEquals(false, $thread->getMessages()[0]->getMessageMetaForParticipant('receiver_2')->isRead());
    }

    private function getCreateThreadCommand()
    {
        return new CreateThread($this->getThreadId(), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
    }


}
