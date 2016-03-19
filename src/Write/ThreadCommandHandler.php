<?php

namespace Milio\Message\Write;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventSourcing\EventSourcingRepository;
use Milio\Message\Write\Model\Thread;
use Milio\Message\Write\Commands\CreateThread;

class ThreadCommandHandler extends CommandHandler
{
    public function __construct(EventSourcingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreateThread(CreateThread $command)
    {
        $aggregate = Thread::createThread($command);
        $this->repository->save($aggregate);
    }
}
