<?php

namespace Milio\Message\Tests\Bundle\DependencyInjection;

use Milio\Message\Bundle\DependencyInjection\MilioMessageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class MilioMessageExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $containerBuilder;

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "message_class" at path "milio_message" must be configured.
     */
    public function it_needs_a_mesage_class()
    {
        $loader = new MilioMessageExtension();
        $config = $this->getEmptyConfig();
        unset($config['message_class']);
        $loader->load([$config], new ContainerBuilder());
    }



    protected function createEmptyConfiguration()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader = $this->getLoader();
        $config = $this->getEmptyConfig();
        $loader->load([$config], $this->containerBuilder);
    }

    /**
     * gets an empty config
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
thread_class: \Acme\MyBundle\Entity\Thread
thread_meta_class: \Acme\MyBundle\Entity\ThreadMeta
message_class: \Acme\MyBundle\Entity\Message
message_meta_class: \Acme\MyBundle\Entity\MessageMeta

EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Asserts that a parameter key has a certain value
     *
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->containerBuilder->getParameter($key));
    }

    /**
     * Asserts that a definition exists
     *
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->containerBuilder->hasDefinition($id) || $this->containerBuilder->hasAlias($id)));
    }
}
