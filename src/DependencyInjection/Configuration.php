<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('splash');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()

            //====================================================================//
            // Connexions
            //====================================================================//
            ->arrayNode('connections')
            ->arrayPrototype()
            ->children()
            ->scalarNode('id')->isRequired()->cannotBeEmpty()
            ->info('Your Splash Server Identifier. Given when creating a new server.')
            ->end()
            ->scalarNode('key')->isRequired()->cannotBeEmpty()
            ->info('Your Splash Server Encyption Key. Given when creating a new server.')
            ->end()
            ->scalarNode('name')->isRequired()->cannotBeEmpty()
            ->info('Your Splash Server Name')
            ->end()
            ->scalarNode('server_host')->defaultValue(null)
            ->info('Override Auto-Detected Hostname for your Server')
            ->end()
            ->scalarNode('host')->defaultValue('https://www.splashsync.com/ws/soap')
            ->info('Expert Mode. Set this url to Splash server.')
            ->end()
            ->scalarNode('connector')->defaultValue('standalone')
            ->info('Name of the connector to use for this Connection.')
            ->end()
            ->variableNode('config')->defaultValue(array())
            ->info('Connector configuration array.')
            ->end()
            ->end()
            ->end()
            ->end()

            //====================================================================//
            // Notification Roles
            //====================================================================//
            ->arrayNode('notify')
            ->prototype('scalar')->end()
            ->defaultValue(array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_ADMINISTRATION_ACCESS'))
            ->info('List of Users Roles who will see Splash Notifications')
            ->end()

            //====================================================================//
            // Cache Storage for Connectors Configuration
            //====================================================================//
            ->arrayNode('cache')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')
            ->isRequired()
            ->defaultTrue()
            ->info('Enable Caching Connector Configuration')
            ->end()
            ->scalarNode('lifetime')
            ->isRequired()
            ->defaultNull()
            ->info('Lifetime for Caching Configuration in Seconds')
            ->end()
            ->end()
            ->end()

            //====================================================================//
            // Local Parameters
            //====================================================================//
            ->arrayNode('infos')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('company')->defaultValue('Undefined')->end()
            ->scalarNode('address')->defaultValue('Undefined')->end()
            ->scalarNode('zip')->defaultValue('Undefined')->end()
            ->scalarNode('town')->defaultValue('Undefined')->end()
            ->scalarNode('country')->defaultValue('Undefined')->end()
            ->scalarNode('www')->defaultValue('Undefined')->end()
            ->scalarNode('email')->defaultValue('Undefined')->end()
            ->scalarNode('phone')->defaultValue('Undefined')->end()
            ->scalarNode('ico')->defaultValue(null)->end()
            ->scalarNode('logo')->defaultValue(null)->end()
            ->end()
            ->end()

            //====================================================================//
            // Test Configuration
            //====================================================================//
            ->variableNode('test')->defaultValue(array())
            ->info('General Configuration Values for Phpunit Testing')
            ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
