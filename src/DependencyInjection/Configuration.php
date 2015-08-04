<?php

namespace LogStream\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('log_stream');

        $root
            ->children()
                ->scalarNode('url')->isRequired()->end()
            ->end()
        ;

        return $builder;
    }
}
