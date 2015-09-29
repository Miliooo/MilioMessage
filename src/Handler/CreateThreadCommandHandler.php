<?php

namespace Milio\Message\Handler;

use Milio\Message\Commands\CreateThread;
use Milio\Message\Validators\CreateThreadValidatorInterface;
use Milio\Message\Errors\CreateThreadError;
use Milio\Message\Commands\ReplyToThread;

class CreateThreadCommandHandler
{
    private $validator;

    /**
     * @param CreateThreadValidatorInterface $validator
     */
    public function __construct(CreateThreadValidatorInterface $validator, $threadClass)
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

        //we should probably split the thread and the message as was done in the fos message bundle
        //http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#transitive-persistence-cascade-operations
        //$thread = Thread::createNewThread($command);
        //store the thread
        //$this->threadRepository->save($thread)

        //$message = $thread->getMessages()->last();
        //store the message
        //$this->messageRepository->save($thread->getMessages()->last);
    }

    //needs also a validator service
    public function handleReplyToThreadCommand(ReplyToThread $command)
    {
        //thread repository get thread
        //$thread = $this->threadRepository->get($thread)

        //$thread = thread->replyToThread($command)
        //$this->threadRepository->save($thread)
        //$this->messageRepository->save($thread->getMessages()->last)
    }
}