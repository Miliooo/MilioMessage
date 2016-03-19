<?php

namespace Read\Model\Dbal;

use Milio\Message\Read\Model\Dbal\ViewMessage;
use Milio\Message\Read\Model\Dbal\ViewThread;
use Milio\Message\TestUtils\WriteTestUtils;

class ViewMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_thread_from_message_added()
    {
        $event = WriteTestUtils::getThreadCreatedEvent();
        $thread = ViewThread::createFromEvent($event);

        $message = WriteTestUtils::getFirstMessageAddedEvent();

        $thread = ViewMessage::createFromEvent($message, $thread);

        //see test utils get first message added
        $sender = WriteTestUtils::THREAD_STARTER;
        $receivers = [WriteTestUtils::RECEIVER_1, WriteTestUtils::RECEIVER_2];

        $this->assertCount(1, $thread->getMessages());
        $this->assertSenderHasReadMessage($thread->getMessages()[0], $sender);
        $this->assertReceiversHaveNotReadMessage($thread->getMessages()[0], $receivers);
        $this->assertThreadIsInboxForReceivers($thread, $receivers);
        $this->assertThreadNotInboxForSender($thread, $sender);

        //check the message
        $lastMessage = $thread->getMessages()[0];
        $this->assertEquals(WriteTestUtils::BODY_1, $lastMessage->getBody());
        $this->assertEquals($sender, $lastMessage->getSender());
        $this->assertEquals(new \DateTime(WriteTestUtils::CREATED_AT), $lastMessage->getCreatedAt());
    }

    /**
     * @test
     */
    public function to_array_and_back_returns_the_same_message()
    {
        $event = WriteTestUtils::getThreadCreatedEvent();
        $thread = ViewThread::createFromEvent($event);

        $message = WriteTestUtils::getFirstMessageAddedEvent();
        $thread = ViewMessage::createFromEvent($message, $thread);

        $lastMessage = $thread->getMessages()[0];

        $array = $lastMessage->toArray();
        $message = ViewMessage::fromArray($array);

        $this->assertEquals($message, $lastMessage);
    }

    /**
     * @param $thread
     */
    protected function assertSenderHasReadMessage(ViewMessage $viewMessage, $sender)
    {
        $this->assertTrue($viewMessage->getMessageMetaForParticipant($sender)->isRead());
    }

    private function assertReceiversHaveNotReadMessage(ViewMessage $viewMessage, $receivers)
    {
        foreach ($receivers as $receiver) {
            $this->assertFalse($viewMessage->getMessageMetaForParticipant($receiver)->isRead());
        }

    }

    private function assertThreadIsInboxForReceivers(ViewThread $thread, $receivers)
    {
        foreach ($receivers as $receiver) {
            $this->assertTrue($thread->getThreadMetaForParticipant($receiver)->isInbox());
        }
    }

    private function assertThreadNotInboxForSender(ViewThread $thread, $sender)
    {
        $this->assertFalse($thread->getThreadMetaForParticipant($sender)->isInbox());
    }
}
