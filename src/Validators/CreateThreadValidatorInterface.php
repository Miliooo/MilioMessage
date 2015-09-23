<?php

namespace Milio\Message\Validators;

use Milio\Message\Errors\CreateThreadError;
use Milio\Message\Commands\CreateThread;

interface CreateThreadValidatorInterface
{
    /**
     * Validates the create new thread command.
     *
     * This should not only validate
     *  - the title
     *  - the body
     *  - whether the given sender has enough rights to message the given recipients
     *  ⁻ that the sender and the recipients are not the same, this will not work in our current model
     *
     * @param CreateThread $command
     *
     * @return CreateThreadError|null
     */
    public function validate(CreateThread $command);
}