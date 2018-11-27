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
use Splash\Bundle\Models\AbstractConnector;

/**
 * @abstract    Make Class Connectors Manager Aware
 */
trait ConnectorsManagerAwareTrait
{
    /**
     * @abstract    Splash Connectors Manager
     *
     * @var ConnectorsManager
     */
    private $manager;
    
    /**
     * @var string
     */
    private $serverId;

    //====================================================================//
    //  ACCESS TO CONNECTORS MANAGER
    //====================================================================//
    
    /**
     * @abstract    Set Connector Manager
     *
     * @param   ConnectorsManager $manager
     *
     * @return  $this
     */
    public function setManager(ConnectorsManager $manager)
    {
        $this->manager  =   $manager;

        return $this;
    }

    /**
     * @abstract    Set Connector Manager
     *
     * @return  ConnectorsManager
     */
    private final function getManager()
    {
        return $this->manager;
    }
    
    //====================================================================//
    //  SERVER IDENTIFICATION
    //====================================================================//
    
    /**
     * @abstract    Setup Current Server Id
     *
     * @param       string $serverId
     *
     * @return      void
     */
    public function setServerId(string $serverId)
    {
        $this->serverId    =   $serverId;
    }
    
    /**
     * @abstract    Get Current Server Id
     *
     * @return      string
     */
    public function getServerId()
    {
        return $this->serverId;
    }
    
    /**
     * @abstract    Get Webservice Host
     *
     * @return  AbstractConnector|null
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
     *
     * @return  string|null
     */
    public function getWebserviceId()
    {
        return $this->getManager()->getWebserviceId($this->getServerId());
    }
    
    /**
     * @abstract    Get Webservice Key
     *
     * @return  string|null
     */
    public function getWebserviceKey()
    {
        return $this->getManager()->getWebserviceKey($this->getServerId());
    }
    
    /**
     * @abstract    Get Webservice Host
     *
     * @return  string|null
     */
    public function getWebserviceHost()
    {
        return $this->getManager()->getWebserviceHost($this->getServerId());
    }
    
    /**
     * @abstract    Get List of Available Servers
     *
     * @return      array
     */
    protected function getServersNames()
    {
        return $this->getManager()->getServersNames();
    }
}
