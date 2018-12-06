<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Local;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;
use Splash\Core\SplashCore  as Splash;

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
     * @param ConnectorsManager $manager
     *
     * @return $this
     */
    public function setManager(ConnectorsManager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    //====================================================================//
    //  SERVER IDENTIFICATION
    //====================================================================//

    /**
     * @abstract    Setup Current Server Id
     *
     * @param string $serverId
     */
    public function setServerId(string $serverId)
    {
        $this->serverId = $serverId;
    }

    /**
     * @abstract    Get Current Server Id
     *
     * @return string
     */
    public function getServerId()
    {
        return $this->serverId;
    }

    /**
     * @abstract    Get Webservice Host
     *
     * @throws Exception
     *
     * @return AbstractConnector
     */
    public function getConnector()
    {
        //====================================================================//
        // Load Connector From Manager
        $connector = $this->getManager()->get($this->getServerId());
        if (!$connector) {
            throw new Exception(
                sprintf(
                    'Unable to Load Requested Connector : %s',
                    $this->getManager()->getConnectorName($this->getServerId())
                )
            );
        }
        //====================================================================//
        // Return Connector
        return $connector;
    }

    //====================================================================//
    //  ACCESS SERVER CONFIGURATION
    //====================================================================//

    /**
     * @abstract    Get Webservice Id
     *
     * @return null|string
     */
    public function getWebserviceId()
    {
        return $this->getManager()->getWebserviceId($this->getServerId());
    }

    /**
     * @abstract    Get Webservice Key
     *
     * @return null|string
     */
    public function getWebserviceKey()
    {
        return $this->getManager()->getWebserviceKey($this->getServerId());
    }

    /**
     * @abstract    Get Webservice Host
     *
     * @return null|string
     */
    public function getWebserviceHost()
    {
        return $this->getManager()->getWebserviceHost($this->getServerId());
    }

    /**
     * @abstract    Get Webservice Name
     *
     * @return null|string
     */
    public function getServerName()
    {
        return $this->getManager()->getServerName($this->getServerId());
    }
    
    /**
     * @abstract    Get Server Host url
     *
     * @return null|string
     */
    public function getServerHost()
    {
        return $this->getManager()->getServerHost($this->getServerId());
    }

    /**
     * @abstract    Get List of Available Servers
     *
     * @return array
     */
    protected function getServersNames()
    {
        return $this->getManager()->getServersNames();
    }

    /**
     * @abstract    Set Connector Manager
     *
     * @return ConnectorsManager
     */
    final private function getManager()
    {
        return $this->manager;
    }
}
