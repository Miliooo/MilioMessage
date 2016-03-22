<?php

namespace Milio\Message\Read\Model\Dbal;

use Milio\Message\TestUtils\WriteTestUtils;

class ViewThreadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_thread_from_thread_created_event()
    {
        $event = WriteTestUtils::getThreadCreatedEvent();
        $thread = ViewThread::createFromEvent($event);

        $this->assertValidThread($thread);
    }

    /**
     * @test
     */
    public function it_creates_a_thread_from_an_array()
    {
        $metaSender = new ViewThreadMeta(WriteTestUtils::THREAD_ID, WriteTestUtils::THREAD_STARTER);
        $metaSender->setIsInbox(false);
        $metaSender->setUnreadCount(0);
        $metaSender->setLastMessageDate(new \DateTime(WriteTestUtils::CREATED_AT));

        $metaReceiver1 = new ViewThreadMeta(WriteTestUtils::THREAD_ID, WriteTestUtils::RECEIVER_1);
        $metaReceiver1->setIsInbox(false);
        $metaReceiver1->setUnreadCount(0);
        $metaReceiver1->setLastMessageDate(new \DateTime(WriteTestUtils::CREATED_AT));

        $metaReceiver2 = new ViewThreadMeta(WriteTestUtils::THREAD_ID, WriteTestUtils::RECEIVER_2);
        $metaReceiver2->setIsInbox(false);
        $metaReceiver2->setUnreadCount(0);
        $metaReceiver2->setLastMessageDate(new \DateTime(WriteTestUtils::CREATED_AT));

        $createdAtDt = new \DateTime(WriteTestUtils::CREATED_AT);
        $threadArray = [
            'thread_id' => WriteTestUtils::THREAD_ID,
            'sender' => WriteTestUtils::THREAD_STARTER,
            'subject' => WriteTestUtils::SUBJECT,
            'created_at' => $createdAtDt->format(\DateTime::ISO8601),
            'metas' => [$metaSender->toArray(), $metaReceiver1->toArray(), $metaReceiver2->toArray()],
        ];

        $thread = ViewThread::fromArray($threadArray);
        $this->assertValidThread($thread);
    }

    /**
     * @test
     */
    public function it_converts_a_thread_to_an_array_and_back_and_returns_a_valid_thread()
    {
        $event = WriteTestUtils::getThreadCreatedEvent();
        $thread = ViewThread::createFromEvent($event);
        $array = $thread->toArray();
        $this->assertValidThread(ViewThread::fromArray($array));
    }

    /**
     * Asserts a thread is valid given the WriteTestUtils constants
     *
     * @param ViewThread $thread
     */
    protected function assertValidThread(ViewThread $thread)
    {
        $this->assert_a_thread_has_a_subject($thread, WriteTestUtils::SUBJECT);
        $this->assert_thread_has_meta_for_each_participant($thread, 3); //mark, john, sophie
        $this->assert_get_participants(
            $thread,
            [
                WriteTestUtils::THREAD_STARTER,
                WriteTestUtils::RECEIVER_1,
                WriteTestUtils::RECEIVER_2
            ]
        );
    }

    /**
     * @param ViewThread $thread
     * @param int        $count
     */
    protected function assert_thread_has_meta_for_each_participant(ViewThread $thread, $count)
    {
        $this->assertEquals($count, count($thread->getThreadMeta()), 'A thread has meta for each participant');
    }

    /**
     * @param ViewThread $thread
     * @param string $expectedSubject
     */
    protected function assert_a_thread_has_a_subject(ViewThread $thread, $expectedSubject)
    {
        $this->assertEquals($thread->getSubject(), $expectedSubject, 'subjects do not match');
    }

    /**
     * @param ViewThread $thread
     * @param array $participants
     */
    protected function assert_get_participants(ViewThread $thread, $participants)
    {
        $sophie = WriteTestUtils::THREAD_STARTER;
        $mark = WriteTestUtils::RECEIVER_1;
        $john =  WriteTestUtils::RECEIVER_2;

        $this->assertEquals(count($participants), count($thread->getParticipants()), 'thread has 3 participants');

        foreach([$sophie, $mark, $john] as $single) {
            $this->assertTrue(in_array($single, $thread->getParticipants(), true), 'sender is participant');
        }

        $this->assertFalse(in_array('not_participant', $thread->getParticipants(), true), 'not_participant is not a participant');


        $otherParticipants = $thread->getOtherParticipants($john);
        $this->assertEquals(2, count($thread->getOtherParticipants($john)));

        foreach([$sophie, $mark] as $other) {
            $this->assertTrue(in_array($other, $otherParticipants, true), $other.' is other participant');
        }

        $this->assertFalse(in_array($john, $otherParticipants, true), 'john is not an other participant');
    }
}
