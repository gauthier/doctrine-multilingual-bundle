<?php


namespace Gauthier\MultilingualBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $root = $builder->root('doctrine_multilingual');
        $root
            ->children()
                ->arrayNode('languages')
                ->children()
                    ->arrayNode('supported')
                    ->isRequired()
                    ->end()
                    ->scalarNode('default')
                    ->end()
                    ->scalarNode('fallback')
                    ->end()
                ->end()
                ->arrayNode('routes')
                ->end()
            ->end();

        return $builder;
    }

}
