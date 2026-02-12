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
use Splash\Bundle\Dictionary\StandaloneServiceTags;
use Splash\Core\Interfaces\Extensions\ObjectExtensionInterface;
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
        $loader->load('forms.yaml');

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
        $definition = $container->getDefinition(StandaloneServiceTags::ID);
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds(StandaloneServiceTags::OBJECT);
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
                //====================================================================//
                // Register Provided Features Scopes
                if (!empty($scopes = self::toStrings($attributes["scopes"] ?? null))) {
                    foreach ($scopes as $scope) {
                        if (is_string($scope) && !empty($scope)) {
                            $definition->addMethodCall('registerScope', array($scope));
                        }
                    }
                }
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
        $definition = $container->getDefinition(StandaloneServiceTags::ID);
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds(StandaloneServiceTags::EXTENSION);
        //====================================================================//
        // Register Objects Extension
        foreach ($taggedObjects as $id => $serviceTags) {
            foreach ($serviceTags as $attributes) {
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
                //====================================================================//
                // Register Provided Features Scopes
                if (!empty($scopes = self::toStrings($attributes["scopes"] ?? null))) {
                    foreach ($scopes as $scope) {
                        if (is_string($scope) && !empty($scope)) {
                            $definition->addMethodCall('registerScope', array($scope));
                        }
                    }
                }
            }
        }
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
        $definition = $container->getDefinition(StandaloneServiceTags::ID);
        //====================================================================//
        // Load List of Tagged Objects Services
        $taggedObjects = $container->findTaggedServiceIds(StandaloneServiceTags::ACTION);
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
        $definition = $container->getDefinition(StandaloneServiceTags::ID);
        //====================================================================//
        // Load List of Tagged Widget Services
        $taggedWidgets = $container->findTaggedServiceIds(StandaloneServiceTags::WIDGET);
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

    /**
     * Convert String or Array to Array of Strings
     *
     * @param null|string|string[] $value
     *
     * @return string[]
     */
    private static function toStrings(null|string|array $value): array
    {
        if (is_null($value)) {
            return array();
        }
        if (is_string($value)) {
            $value = explode(",", $value);
        }

        return array_map('trim', $value);
    }
}
