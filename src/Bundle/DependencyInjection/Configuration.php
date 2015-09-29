<?php

namespace Milio\Message\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('milio_message');
        $rootNode
            ->children()
            ->scalarNode('thread_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('thread_meta_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('message_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('message_meta_class')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}