<?php

namespace Milio\Message\Errors;

class CreateThreadError
{
    private $key;
    private $message;

    public function __construct($key, $message)
    {
        $this->key = $key;
        $this->message = $message;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getMessage()
    {
        return $this->message;
    }
}