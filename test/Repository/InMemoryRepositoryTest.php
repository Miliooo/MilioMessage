<?php

namespace Milio\Message\Repository;

use Milio\Message\Commands\CreateThread;
use Milio\Message\Model\Thread;
use Milio\Message\Model\ThreadId;

class InMemoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryRepository
     */
    private $repository;

    public function setUp() {
        $this->repository = new InMemoryRepository();
    }

    /**
     * @test
     */
    public function it_can_save_and_find_a_thread()
    {
        $createCommand = new CreateThread(new ThreadId('foo'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
        $thread = Thread::createNewThread($createCommand);
        $this->repository->save($thread);
        $result = $this->repository->find(new ThreadId('foo'));
        $this->assertEquals($thread, $result);
        $this->assertEquals(3, count($result->getParticipants()));
        $this->assertEquals(1, $result->getMessages()->count());
    }

    /**
     * @test
     */
    public function it_returns_null_when_not_finding_a_thread()
    {
        $this->assertNull($this->repository->find(new ThreadId('not_existing')));
    }

    /**
     * @test
     */
    public function the_unread_message_count_for_receivers_is_1()
    {
        $createCommand = new CreateThread(new ThreadId('foo'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
        $thread = Thread::createNewThread($createCommand);
        $this->repository->save($thread);

        $this->assertEquals(1, $this->repository->getUnreadMessageCountForParticipant('receiver_1'), 'receiver has one unread message');
        $this->assertEquals(0, $this->repository->getUnreadMessageCountForParticipant('sender_id'), 'sender has zero unread messages');
        $this->assertEquals(0, $this->repository->getUnreadMessageCountForParticipant('non_participant'), 'user with no threads has zero unread messages');
    }

    /**
     * @test
     */
    public function the_receivers_have_one_inbox_thread()
    {
        $createCommand = new CreateThread(new ThreadId('foo'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
        $thread = Thread::createNewThread($createCommand);
        $this->repository->save($thread);
        $result = $this->repository->getInboxThreadsForParticipant('receiver_1');
        $this->assertTrue(is_array($result));
        $this->assertEquals($thread, $result[0]);
    }

    /**
     * @test
     */
    public function the_sender_of_the_thread_has_zero_inbox_threads()
    {
        $createCommand = new CreateThread(new ThreadId('foo'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
        $thread = Thread::createNewThread($createCommand);
        $this->repository->save($thread);
        $result = $this->repository->getInboxThreadsForParticipant('sender_id');

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function non_participater_has_zero_inbox_threads()
    {
        $createCommand = new CreateThread(new ThreadId('foo'), 'sender_id', ['receiver_1', 'receiver_2'], 'this is the title', 'message', new \DateTime('2011-01-01'));
        $thread = Thread::createNewThread($createCommand);
        $this->repository->save($thread);
        $result = $this->repository->getInboxThreadsForParticipant('non_participater');

        $this->assertNull($result);
    }
}
