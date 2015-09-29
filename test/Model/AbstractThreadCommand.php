<?php

namespace Milio\Message\Tests\Model;

use Milio\Message\Model\ThreadId;

class AbstractThreadCommand extends \PHPUnit_Framework_TestCase
{
    final protected function getThreadId()
    {
        return new ThreadId('404');
    }
}