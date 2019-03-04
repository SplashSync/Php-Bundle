<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Events\UpdateConfigurationEvent;
use Splash\Core\SplashCore as Splash;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * @abstract    Core Configuration for Spash Connectors Manager
 */
trait ConfigurationTrait
{
    private static $cacheCfgKey = 'splash.server.config.';

    /**
     * Splash Connectors Configuration Array.
     *
     * @var array
     */
    private $configuration;

    /**
     * Symfony File Cache Adapter.
     *
     * @var FilesystemCache
     */
    private $cache;

    /**
     * @abstract    Get List of Available Servers
     *
     * @return array
     */
    public function getServersNames()
    {
        $response = array();
        //====================================================================//
        //  Walk on Configured Servers
        foreach ($this->configuration['connections'] as $serverId => $configuration) {
            $response[$serverId] = $configuration['name'];
        }

        return $response;
    }

    /**
     * @abstract    Check if Serveur Configuration Exists
     *
     * @param string $serverId
     *
     * @return bool
     */
    public function hasServerConfiguration(string $serverId): bool
    {
        return isset($this->configuration['connections'][$serverId]);
    }

    /**
     * @abstract    Get Connector Configuration for a Specified Server
     *
     * @param string $serverId
     *
     * @return array
     */
    public function getServerConfiguration(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return array();
        }
        //====================================================================//
        //  Populate Servers Config From Parameters
        $configuration = $this->configuration['connections'][$serverId]['config'];
        //====================================================================//
        //  Complete Servers Config From Cache
        if ($this->configuration['cache']['enabled']) {
            $configuration = array_replace_recursive(
                $configuration,
                $this->getConnectorConfigurationFromCache($serverId)
            );
        }

        return $configuration;
    }

    /**
     * @abstract    Get List Of Server Configurations Available
     *
     * @return array
     */
    public function getServerConfigurations()
    {
        return array_keys($this->configuration['connections']);
    }

    /**
     * @abstract    Get Webservice Id for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceId(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['id'];
    }

    /**
     * @abstract    Get Webservice Key for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceKey(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['key'];
    }

    /**
     * @abstract    Get Webservice Host for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceHost(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['host'];
    }

    /**
     * @abstract    Get Public Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getServerName(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['name'];
    }

    /**
     * @abstract    Get Override Host Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getServerHost(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['server_host'];
    }

    /**
     * @abstract    Get Connector Service Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getConnectorName(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['connector'];
    }

    /**
     * @abstract    Check if Connector Exists for this WebService Id
     *
     * @param string $webServiceId
     *
     * @return false|string
     */
    public function hasWebserviceConfiguration(string $webServiceId)
    {
        foreach ($this->configuration['connections'] as $serverId => $configuration) {
            if ($configuration['id'] == $webServiceId) {
                return $serverId;
            }
        }

        return false;
    }

    /**
     * @abstract    Return List of Servers Using a Connector
     *
     * @param string $connectorName
     *
     * @return array
     */
    public function getConnectorConfigurations(string $connectorName)
    {
        $response = array();
        //====================================================================//
        //  Search in Configured Servers
        foreach ($this->configuration['connections'] as $serverId => $configuration) {
            if ($configuration['connector'] != $connectorName) {
                continue;
            }
            //====================================================================//
            //  Populate Servers Config From Parameters
            $response[$serverId] = $configuration;
            //====================================================================//
            //  Complete Servers Config From Cache
            if ($this->configuration['cache']['enabled']) {
                $response[$serverId] = array_replace_recursive(
                    $configuration,
                    $this->getConnectorConfigurationFromCache($serverId)
                );
            }
        }

        return $response;
    }

    /**
     * @abstract    On Connector Configuration Update Event
     *
     * @param UpdateConfigurationEvent $event
     */
    public function onUpdateEvent(UpdateConfigurationEvent $event)
    {
        //====================================================================//
        // Check if Cache is Enabled
        if (!$this->configuration['cache']['enabled']) {
            Splash::log()->war('[Splash] Cache is Disabled');

            return;
        }
        //====================================================================//
        // Check if Filesystem Cache Exists
        if (!isset($this->cache)) {
            $this->cache = new FilesystemCache();
        }

        //====================================================================//
        // Detect Pointed Server Host
        $serverId = $this->hasWebserviceConfiguration($event->getWebserviceId());
        if ($serverId) {
            //====================================================================//
            // Update Configuration in Cache
            $this->cache->set(
                static::$cacheCfgKey.$serverId,
                $event->getConfiguration(),
                $this->configuration['cache']['lifetime']
            );
        }
        //====================================================================//
        // Stop Event Propagation
        $event->stopPropagation();
    }

    /**
     * @abstract    Fetch Connector Configuration from System Cache
     *
     * @param string $serverId
     *
     * @return array
     */
    protected function getConnectorConfigurationFromCache(string $serverId)
    {
        //====================================================================//
        // Check if Cache is Enabled
        if (!$this->configuration['cache']['enabled']) {
            return array();
        }
        //====================================================================//
        // Check if Filesystem Cache Exists
        if (!isset($this->cache)) {
            $this->cache = new FilesystemCache();
        }
        //====================================================================//
        //  Search in Cache
        if (!$this->cache->has(static::$cacheCfgKey.$serverId)) {
            return array();
        }

        return $this->cache->get(static::$cacheCfgKey.$serverId);
    }

    /**
     * @abstract    Set Splash Bundle Core Configuration
     *
     * @param array $configuration
     *
     * @return $this
     */
    private function setCoreConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
