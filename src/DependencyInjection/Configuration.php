<?php


namespace Gauthier\MultilingualBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('doctrine_multilingual');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('doctrine_multilingual', 'array');
        }

        $rootNode
            ->children()
            ->arrayNode('languages')
            ->prototype('scalar')
            // ->defaultValue(['en'])
            ->end()
            ->end()
            ->scalarNode('default')
            ->defaultValue('en')
            ->end()
            ->scalarNode('fallback')
            ->defaultValue('end')
            ->end()
           
            ->arrayNode('routes')
            ->beforeNormalization()
            ->ifArray()
            ->then(function ($values) {
                $return = [];
                foreach ($values as $value) {
                    foreach ($value as $from => $to) {
                        $return[] = ['from' => $from, 'to' => $to];
                    }
                }
                return $return;
            })
            ->end()
            ->prototype('array')
            ->children()
            ->scalarNode('from')->end()
            ->scalarNode('to')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }

}
