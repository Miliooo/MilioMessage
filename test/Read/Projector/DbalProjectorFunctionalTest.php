<?php

namespace Milio\Message\Read\Projector;

use Doctrine\DBAL\DriverManager;
use Milio\Message\Read\Model\Dbal\DbalSchema;
use Milio\Message\Read\Projectors\DbalProjector;
use Milio\Message\Read\Provider\DbalThreadProvider;
use Milio\Message\Read\Saver\DbalThreadSaver;
use Milio\Message\TestUtils\WriteTestUtils;

class DbalProjectorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected static $connection;

    /**
     * @var DbalThreadProvider
     */
    private $threadProvider;
    private $saver;
    /**
     * @var DbalProjector
     */
    private $projector;

    public static function setUpBeforeClass()
    {
    }

    public function setup()
    {
        @unlink( __DIR__.'/apps.db3');
        $connection = DriverManager::getConnection(array('driver' => 'pdo_sqlite', 'path' => __DIR__.'/apps.db3'));

        $schemaManager = $connection->getSchemaManager();
        $schema = $schemaManager->createSchema();

        /** @var \Doctrine\DBAL\Schema\Table[] $tables */
        $tables = DbalSchema::getTables();

        foreach ($tables as $table) {
            $name = $table->getName();

            if (false === $schema->hasTable($name)) {
                $schemaManager->createTable($table);
            }
        }

        $this->threadProvider = new DbalThreadProvider($connection);
        $this->saver = new DbalThreadSaver($connection);
        $this->projector = new DbalProjector($this->threadProvider, $this->saver);
    }

    public function tearDown()
    {
        @unlink( __DIR__.'/apps.db3');
    }

    /**
     * @test
     */
    public function test_valid_thread()
    {
        //replay events on the projector.
        $event = WriteTestUtils::getThreadCreatedEvent();
        $message = WriteTestUtils::getFirstMessageAddedEvent();
        $message2 = WriteTestUtils::getSecondMessageAddedEvent();

        $this->projector->handleThreadCreatedEvent($event);
        $this->projector->handleMessageAddedEvent($message);
        $this->projector->handleMessageAddedEvent($message2);

        //now do inspections given the created and two messages added event.
        $thread = $this->threadProvider->getThread(WriteTestUtils::THREAD_ID);



        $this->assertEquals(WriteTestUtils::THREAD_ID, $thread->getThreadId());
        $this->assertCount(3, $thread->getParticipants());
        $this->assertEquals(2, $thread->getThreadMetaForParticipant(WriteTestUtils::RECEIVER_2)->getUnreadCount());
        $this->assertEquals(1, $thread->getThreadMetaForParticipant(WriteTestUtils::RECEIVER_1)->getUnreadCount());
        $this->assertCount(2, $thread->getMessages());
    }
}
