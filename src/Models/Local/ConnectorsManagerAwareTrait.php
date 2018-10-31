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

namespace Splash\Bundle\Models\Local;

use Exception;

use Splash\Core\SplashCore  as Splash;

use Splash\Bundle\Services\ConnectorsManager;
use Splash\Bundle\Interfaces\ConnectorInterface as Connector;

/**
 * @abstract    Make Class Connectors Manager Aware
 */
trait ConnectorsManagerAwareTrait
{
    /**
     * @abstract    Splash Connectors Manager
     * @var ConnectorsManager
     */
    private $Manager;
    
    /**
     * @var string
     */
    private $ServerId;

    //====================================================================//
    //  ACCESS TO CONNECTORS MANAGER
    //====================================================================//
    
    /**
     * @abstract    Set Connector Manager
     * @param   ConnectorsManager   $Manager
     * @return  $this
     */
    public function setManager(ConnectorsManager $Manager)
    {
        $this->Manager  =   $Manager;
        return $this;
    }

    /**
     * @abstract    Set Connector Manager
     * @return  ConnectorsManager
     */
    private final function getManager()
    {
        return $this->Manager;
    }
    
    //====================================================================//
    //  SERVER IDENTIFICATION
    //====================================================================//
    
    /**
     * @abstract    Setup Current Server Id
     * @param       string  $ServerId
     * @return      void
     */
    private function setServerId(string $ServerId)
    {
        $this->ServerId    =   $ServerId;
    }
    
    /**
     * @abstract    Get Current Server Id
     * @return      string
     */
    public function getServerId()
    {
        return $this->ServerId;
    }
    
    /**
     * @abstract    Setup for Using First Connector Service
     * @return  Connector|null
     */
    public function first()
    {
        //====================================================================//
        // Load Servers Namess
        $Servers    =   $this->getServersNames();
        if (empty($Servers)) {
            throw new Exception("No server Configured for Splash");
        }
        $ServerIds    =   array_keys($Servers);
        $this->setServerId(array_shift($ServerIds));
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
    }
    
    /**
     * @abstract    Identify Connector Service for a Specified WebService Id
     * @param   string      $WebserviceId        Splash WebService Id
     * @return  Connector|null
     */
    public function identify(string $WebserviceId)
    {
        //====================================================================//
        // Seach for This Connection in Local Configuration
        $ServerId   =   $this->getManager()->hasWebserviceConfiguration($WebserviceId);
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$ServerId) {
            return null;
        }
        $this->setServerId($ServerId);
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
        //====================================================================//
        // Return ServerId
        return $ServerId;
    }
    
    /**
     * @abstract    Get Webservice Host
     * @return  Connector
     */
    public function getConnector()
    {
        return $this->getManager()->get($this->getServerId());
    }
    
    //====================================================================//
    //  ACCESS SERVER CONFIGURATION
    //====================================================================//
    
    /**
     * @abstract    Get Webservice Id
     * @return  string|null
     */
    public function getWebserviceId()
    {
        return $this->getManager()->getWebserviceId($this->getServerId());
    }
    
    /**
     * @abstract    Get Webservice Key
     * @return  string|null
     */
    public function getWebserviceKey()
    {
        return $this->getManager()->getWebserviceKey($this->getServerId());
    }
    
    /**
     * @abstract    Get Webservice Host
     * @return  string|null
     */
    public function getWebserviceHost()
    {
        return $this->getManager()->getWebserviceHost($this->getServerId());
    }
    
    /**
     * @abstract    Get List of Available Servers
     * @return      array
     */
    protected function getServersNames()
    {
        return $this->getManager()->getServersNames();
    }
}
