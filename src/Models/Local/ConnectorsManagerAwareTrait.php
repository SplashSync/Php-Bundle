<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
 * Make Class Connectors Manager Aware
 */
trait ConnectorsManagerAwareTrait
{
    /**
     * Splash Connectors Manager
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
     * Set Connector Manager
     *
     * @param ConnectorsManager $manager
     *
     * @return self
     */
    public function setManager(ConnectorsManager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    //====================================================================//
    //  SERVER IDENTIFICATION
    //====================================================================//

    /**
     * Setup Current Server Id
     *
     * @param string $serverId
     *
     * @return self
     */
    public function setServerId(string $serverId): self
    {
        $this->serverId = $serverId;

        return $this;
    }

    /**
     * Get Current Server Id
     *
     * @return string
     */
    public function getServerId()
    {
        return $this->serverId;
    }

    /**
     * Get Webservice Host
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
     * Get Webservice Id
     *
     * @return null|string
     */
    public function getWebserviceId()
    {
        return $this->getManager()->getWebserviceId($this->getServerId());
    }

    /**
     * Get Webservice Key
     *
     * @return null|string
     */
    public function getWebserviceKey()
    {
        return $this->getManager()->getWebserviceKey($this->getServerId());
    }

    /**
     * Get Webservice Host
     *
     * @return null|string
     */
    public function getWebserviceHost()
    {
        return $this->getManager()->getWebserviceHost($this->getServerId());
    }

    /**
     * Get Webservice Name
     *
     * @return null|string
     */
    public function getServerName()
    {
        return $this->getManager()->getServerName($this->getServerId());
    }

    /**
     * Get Server Host url
     *
     * @return null|string
     */
    public function getServerHost()
    {
        return $this->getManager()->getServerHost($this->getServerId());
    }

    /**
     * Get List of Available Servers
     *
     * @return array
     */
    protected function getServersNames()
    {
        return $this->getManager()->getServersNames();
    }

    /**
     * Set Connector Manager
     *
     * @return ConnectorsManager
     */
    final private function getManager()
    {
        return $this->manager;
    }
}
