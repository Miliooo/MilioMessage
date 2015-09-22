<?php

namespace Milio\Message\Handler;

use Milio\Message\Commands\CreateThread;
use Milio\Message\Model\Thread;
use Milio\Message\Validators\CreateThreadValidatorInterface;
use Milio\Message\Errors\CreateThreadError;

class CreateThreadCommandHandler
{
    private $validator;

    public function __construct(CreateThreadValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param CreateThread $command
     * @return CreateThreadError|null
     */
    public function handleCreateNewThreadCommand(CreateThread $command)
    {
        //command validator
        $result = $this->validator->validate($command);
        if($result instanceof CreateThreadError) {
            return $result;
        }

        //create the model
        $thread = Thread::createNewThread($command);

        //a service that knows how to save the thread, i don't care how it gets saved at the moment...

        //maybe return the thread id, so the controller can get more information...
        return null;
    }
}