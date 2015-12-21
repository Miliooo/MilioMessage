<?php

namespace Milio\Message\Model;

use Rhumsaa\Uuid\Uuid;

class AbstractId
{

    private $value;

    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public static function generate()
    {
        return Uuid::uuid4()->toString();
    }
}
