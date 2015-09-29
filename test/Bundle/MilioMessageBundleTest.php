<?php

namespace Milio\Message\Tests\Bundle;

use Milio\Message\Bundle\MilioMessageBundle;

class MilioMessageBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_implements_the_interface()
    {
        $bundle = new MilioMessageBundle();

        $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\Bundle', $bundle);
    }
}
