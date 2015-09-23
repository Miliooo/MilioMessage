<?php

namespace Milio\Message\Model;

class AbstractThreadCommand extends \PHPUnit_Framework_TestCase
{
    final protected function getThreadId()
    {
        return new ThreadId('404');
    }
}