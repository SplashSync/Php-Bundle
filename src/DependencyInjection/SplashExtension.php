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

use Exception;
use Splash\Bundle\Interfaces\AuthenticatorInterface;
use Splash\Bundle\Security\ConnectorAuthenticator;
use Splash\Models\ObjectExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages Splash bundle configuration
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */
class SplashExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('splash', $config);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        //====================================================================//
        // CONFIGURE STANDALONE CONNECTOR
        //====================================================================//

        // Connectors Standalone Objects
        $this->registerStandaloneObjects($container);
        // Connectors Standalone Objects Extensions
        $this->registerStandaloneObjectsExtensions($container);
        // Connectors Standalone Widgets
        $this->registerStandaloneWidgets($container);
        // Connectors Standalone Actions
        $this->registerStandaloneActions($container);

        //====================================================================//
        // CONFIGURE SPLASH BUNDLE AUTHENTICATORS
        //====================================================================//

        // $this->registerAuthenticators($container);
    }

    /**
     * Register Tagged Standalone Connector Actions
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneActions(ContainerBuilder $container): void
    {
        //====================================================================//
        // Load Service Definition
        $definition = $container->getDefinition('splash.connectors.standalone');
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds('splash.standalone.action');
        //====================================================================//
        // Register Objects Services
        foreach ($taggedObjects as $serviceTags) {
            foreach ($serviceTags as $attributes) {
                //====================================================================//
                // Ensure Action Code is set
                if (!isset($attributes["type"])) {
                    throw new Exception(
                        'Tagged Standalone Action as no "type" attribute. Action Type is the last part of Action Url'
                    );
                }
                //====================================================================//
                // Ensure Action Controller is set
                if (!isset($attributes["action"])) {
                    throw new Exception(
                        'Tagged Standalone Action as no "action" attribute. 
                        Action is full controller name to use for this action.'
                    );
                }
                //====================================================================//
                // Add Object Service to Connector
                $definition->addMethodCall(
                    'registerStandaloneAction',
                    array($attributes["type"], $attributes["action"])
                );
            }
        }
    }

    /**
     * Register Tagged Objects Services to Standalone Connector
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneObjects(ContainerBuilder $container): void
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
     * Register Tagged Objects Extension to Standalone Connector
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneObjectsExtensions(ContainerBuilder $container): void
    {
        //====================================================================//
        // Load Service Definition
        $definition = $container->getDefinition('splash.connectors.standalone');
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds('splash.standalone.extension');
        //====================================================================//
        // Register Objects Extension
        foreach (array_keys($taggedObjects) as $id) {
            //====================================================================//
            // Ensure Class is an Object Extension
            if (!in_array(ObjectExtensionInterface::class, class_implements($id) ?: array(), true)) {
                throw new Exception(sprintf(
                    'Tagged Standalone Object Extension must implement %s',
                    ObjectExtensionInterface::class
                ));
            }
            //====================================================================//
            // Add Object Extension to Connector
            $definition->addMethodCall('registerObjectExtension', array(new Reference($id)));
        }
    }

    /**
     * Register Tagged Widgets Services to Standalone Connector
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    private function registerStandaloneWidgets(ContainerBuilder $container): void
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

    //    /**
    //     * Register Tagged Connector Authenticators
    //     *
    //     * @param ContainerBuilder $container
    //     *
    //     * @throws Exception
    //     */
    //    private function registerAuthenticators(ContainerBuilder $container): void
    //    {
    //        //====================================================================//
    //        // Load Service Definition
    //        $definition = $container->getDefinition(ConnectorAuthenticator::class);
    //        //====================================================================//
    //        // Load List of Tagged Objects Services
    //        $taggedObjects = $container->findTaggedServiceIds('splash.connectors.authenticator');
    //        //====================================================================//
    //        // Register Authenticators
    //        foreach (array_keys($taggedObjects) as $id) {
    //            //====================================================================//
    //            // Ensure Class is an Object Extension
    //            if (!in_array(AuthenticatorInterface::class, class_implements($id) ?: array(), true)) {
    //                throw new Exception(sprintf(
    //                    'Tagged Connector Authenticator must implement %s',
    //                    AuthenticatorInterface::class
    //                ));
    //            }
    //            //====================================================================//
    //            // Add Object Extension to Connector
    //            $definition->addMethodCall('registerAuthenticator', array(new Reference($id)));
    //        }
    //    }
}
