<?php

use Milio\Message\Read\Model\Dbal\DbalSchema;
use Milio\Message\Read\Model\Dbal\ViewMessage;
use Doctrine\DBAL\DriverManager;
use Milio\Message\Read\Provider\DbalThreadProvider;
require __DIR__."/../vendor/autoload.php";

//get dbal connection
$connection = DriverManager::getConnection(array('driver' => 'pdo_sqlite', 'path' => __DIR__.'/app.db3'));

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


$inbox = new \Milio\Message\Read\Provider\Folder\InboxThreadProvider($connection);
$data = $inbox->getFolderData('Sophie');

\Symfony\Component\VarDumper\VarDumper::dump($data);

return;





$threadProvider= new DbalThreadProvider($connection);

$messages = $threadProvider->getMessages('61234f19-b8ca-4c54-8002-1615a9087da1');

$controller = new \Milio\Message\Controller\SingleThreadController($threadProvider);

$thread = $controller->getThread('61234f19-b8ca-4c54-8002-1615a9087da1');

echo $thread;

exit();













$threadSaver = new \Milio\Message\Read\Saver\DbalThreadSaver($connection);


$event = \Milio\Message\TestUtils\WriteTestUtils::getThreadCreatedEvent();
$message = \Milio\Message\TestUtils\WriteTestUtils::getFirstMessageAddedEvent();
var_dump($message);
exit('hoi');

$projector = new \Milio\Message\Read\Projectors\DbalProjector($threadProvider, $threadSaver);



$thread = $projector->handleThreadCreatedEvent($event);

$thread = ViewMessage::createFromEvent($message, $thread);


echo json_encode($thread->toArray(), JSON_PRETTY_PRINT);