<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @abstract    This is the class that loads and manages Splash bundle configuration
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */
class SplashExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $container->setParameter('splash', $config);
    }
    
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        //====================================================================//
        // CONFIGURE STANDALONE CONNECTOR
        //====================================================================//
        
        $this->registerStandaloneObjects($container);
        $this->registerStandaloneWidgets($container);
    }
    
    /**
     * @abstract    Register Tagged Objects Services to Standalone Connector
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneObjects(ContainerBuilder $container)
    {
        //====================================================================//
        // Load Service Definition
        $definition = $container->getDefinition('splash.connectors.standalone');
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds('splash.standalone.object');
        //====================================================================//
        // Register Objects Services
        foreach ($taggedObjects as $id => $serviceTags) {
            foreach ($serviceTags as $attributes) {
                //====================================================================//
                // Ensure Object Type is set
                if (!isset($attributes["type"])) {
                    throw new Exception('Tagged Standalone Object Service as no "type" attribute.');
                }
                //====================================================================//
                // Add Object Service to Connector
                $definition->addMethodCall('registerObjectService', array($attributes["type"], new Reference($id)));
            }
        }
    }
    
    /**
     * @abstract    Register Tagged Widgets Services to Standalone Connector
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneWidgets(ContainerBuilder $container)
    {
        //====================================================================//
        // Load Service Definition
        $definition = $container->getDefinition('splash.connectors.standalone');
        //====================================================================//
        // Load List of Tagged Widget Services
        $taggedWidgets = $container->findTaggedServiceIds('splash.standalone.widget');
        //====================================================================//
        // Register Widget Services
        foreach ($taggedWidgets as $id => $serviceTags) {
            foreach ($serviceTags as $attributes) {
                //====================================================================//
                // Ensure Widget Type is set
                if (!isset($attributes["type"])) {
                    throw new Exception('Tagged Standalone Widget Service as no "type" attribute.');
                }
                //====================================================================//
                // Add Widget Service to Connector
                $definition->addMethodCall('registerWidgetService', array($attributes["type"], new Reference($id)));
            }
        }
    }
}
