<?php

namespace Splash\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
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
                // Connexions
                //====================================================================//
                
                ->arrayNode('connections')
                    ->arrayPrototype()
                        ->children()
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
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->info('Your Splash Server Name')
                            ->end()
                            ->scalarNode('host')
                                ->defaultValue("https://www.splashsync.com/ws/soap")
                                ->info('Expert Mode. Set this url to Splash server.')
                            ->end()
                            ->scalarNode('connector')
                                ->defaultValue("splash.connectors.standalone")
                                ->info('Name of the connector to use for this Connection.')
                            ->end()
                            ->variableNode('config')
                                ->defaultValue(array())
                                ->info('Connector configuration array.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                
//                ->booleanNode('use_doctrine')
//                    ->defaultValue(true)
//                    ->info('Enable Doctrine ORM Entity Mapping')
//                ->end()
//                ->booleanNode('use_doctrine_mongodb')
//                    ->defaultValue(false)
//                    ->info('Enable Doctrine MongoDB Documents Mapping')
//                ->end()

                //====================================================================//
                // Notification Roles
                //====================================================================//
                ->arrayNode('notify')
                    ->prototype('scalar')->end()
                    ->defaultValue(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_ADMINISTRATION_ACCESS'])
                    ->info('List of Users Roles who will see Splash Notifications')
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
                        ->scalarNode('ico')->defaultValue(null)->end()
                        ->scalarNode('logo')->defaultValue(null)->end()
                    ->end()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
