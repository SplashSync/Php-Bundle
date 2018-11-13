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
    private $Configuration;
    
    /**
     * @abstract    Set Splash Bundle Core Configuration
     * @param   array   $Configuration
     * @return  $this
     */
    private function setCoreConfiguration(array $Configuration)
    {
        $this->Configuration   =   $Configuration;
        return $this;
    }

    /**
     * @abstract    Get List of Available Servers
     * @return      array
     */
    public function getServersNames()
    {
        $Response   =   array();
        //====================================================================//
        //  Walk on Configured Servers
        foreach ($this->Configuration["connections"] as $ServerId => $Configuration) {
            $Response[$ServerId] =   $Configuration["name"];
        }
        return $Response;
    }
    
    /**
     * @abstract    Check if Serveur Configuration Exists
     * @param   string      $ServerId
     * @return  bool
     */
    public function hasServerConfiguration(string $ServerId) : bool
    {
        return isset($this->Configuration["connections"][$ServerId]);
    }

    /**
     * @abstract    Get Connector Configuration for a Specified Server
     * @param   string      $ServerId
     * @return  array
     */
    public function getServerConfiguration(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return array();
        }
        return $this->Configuration["connections"][$ServerId]["config"];
    }
    
    /**
     * @abstract    Get List Of Server Configurations Available
     * @return  array
     */
    public function getServerConfigurations()
    {
        return array_keys($this->Configuration["connections"]);
    }
    
    /**
     * @abstract    Get Webservice Id for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getWebserviceId(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        return $this->Configuration["connections"][$ServerId]["id"];
    }
    
    /**
     * @abstract    Get Webservice Key for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getWebserviceKey(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        return $this->Configuration["connections"][$ServerId]["key"];
    }

    /**
     * @abstract    Get Webservice Host for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getWebserviceHost(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        return $this->Configuration["connections"][$ServerId]["host"];
    }
    
    /**
     * @abstract    Get Public Name for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getServerName(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        return $this->Configuration["connections"][$ServerId]["name"];
    }
    
    /**
     * @abstract    Get Connector Service Name for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getConnectorName(string $ServerId)
    {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        return $this->Configuration["connections"][$ServerId]["connector"];
    }
    
    /**
     * @abstract    Check if Connector Exists for this WebService Id
     * @param   string      $WebServiceId
     * @return  string|false
     */
    public function hasWebserviceConfiguration(string $WebServiceId)
    {
        foreach ($this->Configuration["connections"] as $ServerId => $Configuration) {
            if ($Configuration["id"] == $WebServiceId) {
                return $ServerId;
            }
        }
        return false;
    }
    
    /**
     * @abstract    Return List of Servers Using a Connector
     * @param   string          $ConnectorName
     * @return  array
     */
    public function getConnectorConfigurations(string $ConnectorName)
    {
        $Response   =   array();
        //====================================================================//
        //  Search in Configured Servers
        foreach ($this->Configuration["connections"] as $ServerId => $Configuration) {
            if ($Configuration["connector"] == $ConnectorName) {
                $Response[$ServerId] =   $Configuration;
            }
        }
        return $Response;
    }
}
