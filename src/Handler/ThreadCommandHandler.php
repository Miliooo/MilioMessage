<?php

namespace Milio\Message\Handler;

use Milio\Message\Commands\CreateNewThreadCommand;
use Milio\Message\Model\Thread;


class ThreadCommandHandler extends CommandHandler
{
    public function __construct(ThreadRepository $threadRepository)
    {
        $this->threadRepository = $threadRepository;
    }

    public function handleCreateNewThreadCommand(CreateNewThreadCommand $command)
    {
       $thread = Thread::createNewThread($command);
    }

}