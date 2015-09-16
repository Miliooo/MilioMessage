<?php

namespace Milio\Message\Validators;

use Milio\Message\Errors\CreateThreadError;

interface CreateThreadValidatorInterface
{
    /**
     * Validates the create new thread command.
     *
     * This should not only validate the title and the content but also
     * whether the given sender has enough rights to message the given recipients
     *
     * @param CreateNewThreadCommand $command
     * @return CreateThreadError|null
     */
    public function validate(CreateNewThreadCommand $command);
}