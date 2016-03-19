<?php

use Milio\Message\Read\Model\Dbal\ViewMessage;

require __DIR__."/../vendor/autoload.php";

$event = \Milio\Message\TestUtils\WriteTestUtils::getThreadCreatedEvent();
$message = \Milio\Message\TestUtils\WriteTestUtils::getFirstMessageAddedEvent();

$projector = new \Milio\Message\Read\Projectors\DbalProjector();

$thread = $projector->handleThreadCreatedEvent($event);
//$thread = $projector->handleMessageAddedEvent($message, $thread);

$thread= ViewMessage::createFromEvent($message, $thread);
echo json_encode($thread->toArray(), JSON_PRETTY_PRINT);