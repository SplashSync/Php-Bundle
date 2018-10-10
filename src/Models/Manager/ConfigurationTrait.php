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
trait ConfigurationTrait {
    
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
    private function setCoreConfiguration(array $Configuration) {
        $this->Configuration   =   $Configuration;
        return $this;
    }

    /**
     * @abstract    Check if Serveur Configuration Exists
     * @param   string      $ServerId
     * @return  bool
     */
    public  function hasServerConfiguration(string $ServerId) : bool 
    {
        return isset($this->Configuration["connections"][$ServerId]);
    }

    /**
     * @abstract    Get Connector Configuration for a Specified Server
     * @param   string      $ServerId
     * @return  array
     */
    public  function getServerConfiguration(string $ServerId) {
        if (!$this->hasServerConfiguration($ServerId)) {
            return array();
        } 
        return $this->Configuration["connections"][$ServerId]["config"];
    }
    
    /**
     * @abstract    Get Webservice Id for a Specified Server
     * @param   string      $ServerId
     * @return  string|null
     */
    public function getServerId(string $ServerId) {
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
    public function getServerKey(string $ServerId) {
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
    public function getServerHost(string $ServerId) {
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
    public function getServerName(string $ServerId) {
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
    public function getConnectorName(string $ServerId) {
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        } 
        return $this->Configuration["connections"][$ServerId]["connector"];
    }
    
}
