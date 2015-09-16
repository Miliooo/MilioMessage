<?php

namespace Milio\Message\Handler;

use Milio\Message\Commands\CreateNewThreadCommand;
use Milio\Message\Model\Thread;
use Milio\Message\Validators\CreateThreadValidatorInterface;

class CreateThreadCommandHandler
{
    private $validator;

    public function __construct(CreateThreadValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handleCreateNewThreadCommand(CreateNewThreadCommand $command)
    {
        //command validator
        $result = $this->validator->validate($command);
        if($result instanceof CreateThreadError) {
            return $result;
        }

        //create the model
        $thread = Thread::createNewThread($command);

        //save the model
    }
}