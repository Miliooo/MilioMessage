<?php

namespace Milio\Message\Validators;

use Milio\Message\Errors\CreateThreadError;
use Milio\Message\Commands\CreateThread;

interface CreateThreadValidatorInterface
{
    /**
     * Validates the create new thread command.
     *
     * This should not only validate the title and the content but also
     * whether the given sender has enough rights to message the given recipients
     *
     * @param CreateThread $command
     * @return CreateThreadError|null
     */
    public function validate(CreateThread $command);
}