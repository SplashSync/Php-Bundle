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

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Events\UpdateConfigurationEvent;
use Splash\Core\Client\Splash;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Core Configuration for Splash Connectors Manager
 */
trait ConfigurationTrait
{
    /**
     * @var string
     */
    private static string $cacheCfgKey = 'splash.server.config.';

    /**
     * Splash Connectors Configuration Array.
     *
     * @var array
     */
    private array $configuration;

    /**
     * Symfony File Cache Adapter.
     *
     * @var FilesystemAdapter
     */
    private FilesystemAdapter $cache;

    /**
     * Get List of Available Servers
     *
     * @return array
     */
    public function getServersNames(): array
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
     * Check if Serveur Configuration Exists
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
     * Get Connector Configuration for a Specified Server
     *
     * @param string $serverId
     *
     * @return array
     */
    public function getServerConfiguration(string $serverId): array
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
     * Get List Of Server Configurations Available
     *
     * @return array
     */
    public function getServerConfigurations(): array
    {
        return array_keys($this->configuration['connections']);
    }

    /**
     * Get Webservice ID for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceId(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['id'];
    }

    /**
     * Get Webservice Key for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceKey(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['key'];
    }

    /**
     * Get Webservice Host for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getWebserviceHost(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['host'];
    }

    /**
     * Get Public Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getServerName(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['name'];
    }

    /**
     * Get Override Host Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getServerHost(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['server_host'];
    }

    /**
     * Get Connector Service Name for a Specified Server
     *
     * @param string $serverId
     *
     * @return null|string
     */
    public function getConnectorName(string $serverId): ?string
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration['connections'][$serverId]['connector'];
    }

    /**
     * Check if Connector Exists for this WebService Id
     *
     * @param string $webServiceId
     *
     * @return null|string
     */
    public function hasWebserviceConfiguration(string $webServiceId): ?string
    {
        foreach ($this->configuration['connections'] as $serverId => $configuration) {
            if ($configuration['id'] == $webServiceId) {
                return $serverId;
            }
        }

        return null;
    }

    /**
     * Return List of Servers Using a Connector
     *
     * @param string $connectorName
     *
     * @return array
     */
    public function getConnectorConfigurations(string $connectorName): array
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
     * On Connector Configuration Update Event
     *
     * @param UpdateConfigurationEvent $event
     *
     * @return void
     */
    public function onUpdateEvent(UpdateConfigurationEvent $event): void
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
            $this->cache = new FilesystemAdapter();
        }
        //====================================================================//
        // Detect Pointed Server Host
        $serverId = $this->hasWebserviceConfiguration($event->getWebserviceId());
        if ($serverId) {
            //====================================================================//
            // Update Configuration in Cache
            $cacheItem = $this->cache->getItem(self::$cacheCfgKey.$serverId);
            $cacheItem->expiresAfter($this->configuration['cache']['lifetime']);
            $cacheItem->set($event->getConfiguration());
            $this->cache->save($cacheItem);
        }
        //====================================================================//
        // Stop Event Propagation
        $event->stopPropagation();
    }

    /**
     * Return Test Configuration Parameters
     *
     * @return array
     */
    public function getTestConfigurations(): array
    {
        $testConfig = $this->getCoreParameter("test");

        return is_array($testConfig) ? $testConfig : array();
    }

    /**
     * Fetch Connector Configuration from System Cache
     *
     * @param string $serverId
     *
     * @return array
     */
    protected function getConnectorConfigurationFromCache(string $serverId): array
    {
        //====================================================================//
        // Check if Cache is Enabled
        if (!$this->configuration['cache']['enabled']) {
            return array();
        }
        //====================================================================//
        // Check if Filesystem Cache Exists
        if (!isset($this->cache)) {
            $this->cache = new FilesystemAdapter();
        }
        //====================================================================//
        //  Search in Cache
        $cacheItem = $this->cache->getItem(self::$cacheCfgKey.$serverId);
        if (!$cacheItem->isHit()) {
            return array();
        }
        $config = $cacheItem->get();

        return is_array($config) ? $config : array();
    }

    /**
     * Set Splash Bundle Core Configuration
     *
     * @param array $configuration
     *
     * @return $this
     */
    protected function setCoreConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Safe Get of A Global Parameter
     *
     * @param string      $key     Global Parameter Key
     * @param mixed       $default Default Parameter Value
     * @param null|string $domain  Parameters Domain Key
     *
     * @return mixed
     */
    protected function getCoreParameter(string $key, $default = null, string $domain = null)
    {
        if ($domain) {
            return $this->configuration[$domain][$key] ?? $default;
        }

        return $this->configuration[$key] ?? $default;
    }
}
