<?php

namespace Splash\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('splash');

        $rootNode
            ->children()
                //====================================================================//
                // COMMON Parameters
                //====================================================================//
                ->scalarNode('id')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Your Splash Server Identifier. Given when creating a new server.')
                ->end()
                ->scalarNode('key')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Your Splash Server Encyption Key. Given when creating a new server.')
                ->end()
                ->scalarNode('host')
                    ->info('Expert Mode. Set this url to route to your didicated server.')
                ->end()
                ->booleanNode('use_doctrine')
                    ->defaultValue(True)
                    ->info('Enable Doctrine ORM Entity Mapping')
                ->end()
                ->booleanNode('use_doctrine_mongodb')
                    ->defaultValue(False)
                    ->info('Enable Doctrine MongoDB Documents Mapping')
                ->end()
                ->booleanNode('multiserver')
                    ->defaultValue(False)
                    ->info('Enable Multi-Server mode. Allow Definition of Multiples Splash Instances on Same Server. (NOT IMPLEMENTED YET!)')
                ->end()

                
                //====================================================================//
                // Local Parameters
                //====================================================================//
                ->arrayNode('infos')
                    ->children()
                        ->scalarNode('company')->defaultValue("Undefined")->end()
                        ->scalarNode('address')->defaultValue("Undefined")->end()
                        ->scalarNode('zip')->defaultValue("Undefined")->end()
                        ->scalarNode('town')->defaultValue("Undefined")->end()
                        ->scalarNode('country')->defaultValue("Undefined")->end()
                        ->scalarNode('www')->defaultValue("Undefined")->end()
                        ->scalarNode('email')->defaultValue("Undefined")->end()
                        ->scalarNode('phone')->defaultValue("Undefined")->end()
                        ->scalarNode('logo')->defaultValue(Null)->end()
                    ->end()
                ->end()
                
                //====================================================================//
                // Static Objects Definitions
                //====================================================================//
                ->arrayNode('objects')
                    ->prototype('scalar')->end()
                ->end()                
                
            ->end()
        ;

        return $treeBuilder;
    }
}
