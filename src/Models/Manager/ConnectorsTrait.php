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

use Exception;
use Splash\Bundle\Models\AbstractConnector as Connector;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;

/**
 * Splash Connector Services Management
 */
trait ConnectorsTrait
{
    /**
     * Splash Connectors Service Array
     *
     * @var array
     */
    private array $connectors;

    /**
     * Add a Connector Service to Manager
     *
     * @param Connector $connectorService
     *
     * @throws Exception
     *
     * @return $this
     */
    public function registerConnectorService(Connector $connectorService): self
    {
        //====================================================================//
        // Read Connector Profile
        $profile = $connectorService->getProfile();
        //====================================================================//
        // Safety Check - Connector Provide a Name
        if (!isset($profile["name"]) || empty($profile["connector"])) {
            throw new Exception("Connector Service Must provide its name in Profile Array['name'].");
        }
        //====================================================================//
        // Safety Check - Connector Name is Unique
        if ($this->has($profile["name"])) {
            throw new Exception("Connector Name Must be Unique.");
        }
        //====================================================================//
        // Register Connector
        $this->connectors[$profile["name"]] = $connectorService;

        return $this;
    }

    /**
     * Check if Connector Exists
     *
     * @param string $connectorName
     *
     * @return bool
     */
    public function has(string $connectorName) : bool
    {
        return isset($this->connectors[$connectorName]);
    }

    /**
     * Get Connector Service & Pass Configuration for a Specified Server
     *
     * @param string $serverId      Server ID or Splash Webservice ID
     * @param array  $configuration
     *
     * @return null|Connector
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function get(string $serverId, array $configuration = array()): ?Connector
    {
        //====================================================================//
        // Identify Requested Connection by Webservice Id
        if ($this->hasWebserviceConfiguration($serverId)) {
            $serverId = $this->hasWebserviceConfiguration($serverId);
        }
        //====================================================================//
        // Safety Check - Server Id Exists
        if (!$serverId || !$this->hasServerConfiguration($serverId)) {
            return null;
        }
        //====================================================================//
        // Safety Check - Connector Service Found
        if (!$this->has((string) $this->getConnectorName($serverId))) {
            return null;
        }
        //====================================================================//
        // Load Connector Service
        $connector = $this->connectors[$this->getConnectorName($serverId)];
        //====================================================================//
        // Setup Connector Configuration
        $connector->configure(
            $serverId,
            $this->getWebserviceId($serverId),
            array_replace_recursive($this->getServerConfiguration($serverId), $configuration)
        );
        //====================================================================//
        // Return Connector
        return $connector;
    }

    /**
     * Get Raw Connector Service without Configuration
     *              Used only to Serve Master Connector Request
     *
     * @param string $connectorName
     *
     * @return null|Connector
     */
    public function getRawConnector(string $connectorName): ?Connector
    {
        //====================================================================//
        // Safety Check - Connector Service Found
        if (!$this->has($connectorName)) {
            return null;
        }

        return $this->connectors[$connectorName];
    }

    /**
     * Identify Connector Service for a Specified WebService ID
     *
     * @param string $webserviceId Splash WebService ID
     *
     * @return null|string
     */
    public function identify(string $webserviceId): ?string
    {
        //====================================================================//
        // Search for This Connection in Local Configuration
        $serverId = $this->hasWebserviceConfiguration($webserviceId);
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$serverId) {
            return null;
        }
        //====================================================================//
        // Setup Splash Local Class
        try {
            $local = Splash::local();
        } catch (Exception $e) {
            return null;
        }
        if ($local instanceof Local) {
            $local->setServerId($serverId);
        }
        //====================================================================//
        // Setup Splash Logger
        Splash::log()->setPrefix((string) $this->getServerName($serverId));
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
        //====================================================================//
        // Return ServerId
        return $serverId;
    }

    /**
     * Identify Connector Service for a Specified Hostname
     *
     * @param string $connectorType Connector Type Name
     * @param string $hostname      Server Hostname
     *
     * @return null|string
     */
    public function identifyByHost(string $connectorType, string $hostname): ?string
    {
        $serverId = null;
        //====================================================================//
        // Walk on All Local Connections
        foreach ($this->getConnectorConfigurations($connectorType) as $index => $config) {
            if (($config["WsHost"] ?? $config["config"]["WsHost"] ?? null) == $hostname) {
                $serverId = (string) $index;

                break;
            }
        }
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$serverId) {
            return null;
        }
        //====================================================================//
        // Setup Splash Local Class
        try {
            $local = Splash::local();
        } catch (Exception $e) {
            return null;
        }
        if ($local instanceof Local) {
            $local->setServerId($serverId);
        }
        //====================================================================//
        // Setup Splash Logger
        Splash::log()->setPrefix((string) $this->getServerName($serverId));
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
        //====================================================================//
        // Return ServerId
        return $serverId;
    }

    /**
     * Get List of Connectors Service that Implements Tracking Interface
     * Used only to Set up Periodic Analyzes
     *
     * @return array
     */
    public function getTrackingConnectors(): array
    {
        $response = array();
        //====================================================================//
        // Walk on Available Connectors
        foreach ($this->connectors as $name => $connector) {
            if (!$connector->isTrackingConnector()) {
                continue;
            }
            $response[$name] = $connector;
        }

        return $response;
    }
}
