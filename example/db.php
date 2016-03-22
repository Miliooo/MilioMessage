<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__."/../vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Milio\Message\Read\Model\Dbal\DbalSchema;
use Milio\Message\Read\Model\Dbal\ViewMessage;

//get the connection, create the tables
$connection = DriverManager::getConnection(array('driver' => 'pdo_sqlite', 'path' => __DIR__.'/app.db3'));
$schemaManager = $connection->getSchemaManager();

$schema = $schemaManager->createSchema();

    /** @var \Doctrine\DBAL\Schema\Table[] $tables */
    $tables = DbalSchema::getTables();

    foreach ($tables as $table) {
        $name = $table->getName();
        echo 'name';
        if (false === $schema->hasTable($name)) {
            $schemaManager->createTable($table);
            echo 'installed table'.$name;
        } else {
        }
    }

//setup services
$dbalThreadProvider = new \Milio\Message\Read\Provider\DbalThreadProvider($connection);
$dbalThreadSaver = new \Milio\Message\Read\Saver\DbalThreadSaver($connection);
$projector = new \Milio\Message\Read\Projectors\DbalProjector($dbalThreadProvider, $dbalThreadSaver);


$thread = $dbalThreadProvider->getThread(\Milio\Message\TestUtils\WriteTestUtils::THREAD_ID);
\Symfony\Component\VarDumper\VarDumper::dump($thread);

if($thread) {
    echo 'deleting thread';
    $connection->delete('milio_thread', ['thread_id' => $thread->getThreadId()]);
    $connection->delete('milio_thread_meta', ['thread_id' => $thread->getThreadId()]);
    foreach ($thread->getMessages() as $message)
    {
        $messageId = $message->getMessageId();
        $connection->delete('milio_message', ['thread_id' => $thread->getThreadId(), 'message_id' => $messageId]);
        $connection->delete('milio_message_meta', ['message_id' => $messageId]);
    }
}



$event = \Milio\Message\TestUtils\WriteTestUtils::getThreadCreatedEvent();
$message = \Milio\Message\TestUtils\WriteTestUtils::getFirstMessageAddedEvent();
$message2 = \Milio\Message\TestUtils\WriteTestUtils::getSecondMessageAddedEvent();
/*
$thread = \Milio\Message\Read\Model\Dbal\ViewThread::createFromEvent($event);
$thread = ViewMessage::createFromEvent($message, $thread);
$thread = ViewMessage::createFromEvent($message2, $thread);

\Symfony\Component\VarDumper\VarDumper::dump($thread);

return;
*/



$projector->handleThreadCreatedEvent($event);
$projector->handleMessageAddedEvent($message);
$projector->handleMessageAddedEvent($message2);

$thread = $dbalThreadProvider->getThread(\Milio\Message\TestUtils\WriteTestUtils::THREAD_ID);
\Symfony\Component\VarDumper\VarDumper::dump($thread);


//$thread = $dbalThreadProvider->getThread(\Milio\Message\TestUtils\WriteTestUtils::THREAD_ID);
//\Symfony\Component\VarDumper\VarDumper::dump($thread);


/*
$connection->insert(
    'milio_thread',
    [
        'thread_id' => $thread->getThreadId(),
        'sender' => $thread->getSender(),
        'subject' => $thread->getSubject(),
        'created_at' => $thread->getDateCreated()
    ],
    [
        \PDO::PARAM_STR,
        \PDO::PARAM_STR,
        \PDO::PARAM_STR,
        'datetime',
    ]
);

foreach($thread->getThreadMeta() as $meta) {
    $connection->insert(
        'milio_thread_meta',
        [
            'thread_id' => $thread->getThreadId(),
            'user_id' => $meta->getUserId(),
            'is_inbox' => $meta->isInbox(),
            'unread_count' => $meta->getUnreadCount(),
            'last_message_date' => $meta->getLastMessageDate()
        ],
        [
            \PDO::PARAM_STR, //thread_id
            \PDO::PARAM_STR, //user_id
            \PDO::PARAM_BOOL, //is_inbox
            \PDO::PARAM_INT, //unread_count
            'datetime', //last_message_date
        ]
    );
}

foreach ($thread->getMessages() as $message) {
    $connection->insert(
        'milio_message',
        [
            'thread_id' => $thread->getThreadId(),
            'message_id' => $message->getMessageId(),
            'sender' => $message->getSender(),
            'body' => $message->getBody(),
            'created_at' => $message->getCreatedAt()
        ],
        [
            \PDO::PARAM_STR, //thread_id
            \PDO::PARAM_STR, //message_id
            \PDO::PARAM_STR, //sender
            \PDO::PARAM_STR, //body
            'datetime', //created_at
        ]
    );

    foreach ($message->getMessageMeta() as $meta) {
        echo 'hierse';
        $connection->insert(
            'milio_message_meta',
            [
                'message_id' => $meta->getMessageId(),
                'user_id' => $meta->getUserId(),
                'is_read' => $meta->isRead(),
            ],
            [
                \PDO::PARAM_STR, //message_id
                \PDO::PARAM_STR, //user_id
                \PDO::PARAM_BOOL //is_read
            ]
        );
    }
}
*/
$dbalThread = $dbalThreadProvider->getThread(\Milio\Message\TestUtils\WriteTestUtils::THREAD_ID);

//\Symfony\Component\VarDumper\VarDumper::dump($dbalThread);

//\Symfony\Component\VarDumper\VarDumper::dump($thread->toArray());
//$viewThread = \Milio\Message\Read\Model\Dbal\ViewThreadMeta::fromArray($data);
//var_dump($viewThread);

$dbalMessages = $dbalThreadProvider->getMessages(\Milio\Message\TestUtils\WriteTestUtils::THREAD_ID);
\Symfony\Component\VarDumper\VarDumper::dump($dbalMessages);