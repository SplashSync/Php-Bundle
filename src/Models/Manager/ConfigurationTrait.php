<?php
/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Models\Manager;

/**
 * @abstract    Core Configuration for Spash Connectors Manager
 */
trait ConfigurationTrait
{
    
    /**
     * Splash Connectors Configuration Array
     * @var array
     */
    private $configuration;
    
    /**
     * @abstract    Set Splash Bundle Core Configuration
     *
     * @param   array $configuration
     *
     * @return  $this
     */
    private function setCoreConfiguration(array $configuration)
    {
        $this->configuration   =   $configuration;

        return $this;
    }

    /**
     * @abstract    Get List of Available Servers
     *
     * @return      array
     */
    public function getServersNames()
    {
        $response   =   array();
        //====================================================================//
        //  Walk on Configured Servers
        foreach ($this->configuration["connections"] as $serverId => $configuration) {
            $response[$serverId] =   $configuration["name"];
        }

        return $response;
    }
    
    /**
     * @abstract    Check if Serveur Configuration Exists
     *
     * @param   string $serverId
     *
     * @return  bool
     */
    public function hasServerConfiguration(string $serverId) : bool
    {
        return isset($this->configuration["connections"][$serverId]);
    }

    /**
     * @abstract    Get Connector Configuration for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  array
     */
    public function getServerConfiguration(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return array();
        }

        return $this->configuration["connections"][$serverId]["config"];
    }
    
    /**
     * @abstract    Get List Of Server Configurations Available
     *
     * @return  array
     */
    public function getServerConfigurations()
    {
        return array_keys($this->configuration["connections"]);
    }
    
    /**
     * @abstract    Get Webservice Id for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  string|null
     */
    public function getWebserviceId(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration["connections"][$serverId]["id"];
    }
    
    /**
     * @abstract    Get Webservice Key for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  string|null
     */
    public function getWebserviceKey(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration["connections"][$serverId]["key"];
    }

    /**
     * @abstract    Get Webservice Host for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  string|null
     */
    public function getWebserviceHost(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration["connections"][$serverId]["host"];
    }
    
    /**
     * @abstract    Get Public Name for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  string|null
     */
    public function getServerName(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration["connections"][$serverId]["name"];
    }
    
    /**
     * @abstract    Get Connector Service Name for a Specified Server
     *
     * @param   string $serverId
     *
     * @return  string|null
     */
    public function getConnectorName(string $serverId)
    {
        if (!$this->hasServerConfiguration($serverId)) {
            return null;
        }

        return $this->configuration["connections"][$serverId]["connector"];
    }
    
    /**
     * @abstract    Check if Connector Exists for this WebService Id
     *
     * @param   string $webServiceId
     *
     * @return  string|false
     */
    public function hasWebserviceConfiguration(string $webServiceId)
    {
        foreach ($this->configuration["connections"] as $serverId => $configuration) {
            if ($configuration["id"] == $webServiceId) {
                return $serverId;
            }
        }

        return false;
    }
    
    /**
     * @abstract    Return List of Servers Using a Connector
     *
     * @param   string $connectorName
     *
     * @return  array
     */
    public function getConnectorConfigurations(string $connectorName)
    {
        $response   =   array();
        //====================================================================//
        //  Search in Configured Servers
        foreach ($this->configuration["connections"] as $serverId => $configuration) {
            if ($configuration["connector"] == $connectorName) {
                $response[$serverId] =   $configuration;
            }
        }

        return $response;
    }
}
