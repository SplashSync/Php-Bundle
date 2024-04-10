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

namespace Splash\Bundle\Models\Local;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Services\ConnectorsManager;

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
    private ConnectorsManager $manager;

    /**
     * @var null|string
     */
    private ?string $serverId;

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
     * @return null|string
     */
    public function getServerId(): ?string
    {
        return $this->serverId ?? null;
    }

    /**
     * Get Webservice Host
     *
     * @throws Exception
     *
     * @return AbstractConnector
     */
    public function getConnector(): AbstractConnector
    {
        //====================================================================//
        // Load Connector From Manager
        $connector = $this->getManager()->get((string) $this->getServerId());
        if (!$connector) {
            throw new Exception(
                sprintf(
                    'Unable to Load Requested Connector : %s',
                    $this->getManager()->getConnectorName($this->getServerId() ?? "None")
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
     * Get Webservice ID
     *
     * @return null|string
     */
    public function getWebserviceId(): ?string
    {
        return $this->getManager()->getWebserviceId((string) $this->getServerId());
    }

    /**
     * Get Webservice Key
     *
     * @return null|string
     */
    public function getWebserviceKey(): ?string
    {
        return $this->getManager()->getWebserviceKey((string) $this->getServerId());
    }

    /**
     * Get Webservice Host
     *
     * @return null|string
     */
    public function getWebserviceHost(): ?string
    {
        return $this->getManager()->getWebserviceHost((string) $this->getServerId());
    }

    /**
     * Get Webservice Name
     *
     * @return null|string
     */
    public function getServerName(): ?string
    {
        return $this->getManager()->getServerName((string) $this->getServerId());
    }

    /**
     * Get Server Host url
     *
     * @return null|string
     */
    public function getServerHost(): ?string
    {
        return $this->getManager()->getServerHost((string) $this->getServerId());
    }

    /**
     * Check if Multiple Servers are Configured
     */
    public function isMultiServerMode(): bool
    {
        static $isMultiServerMode;

        return $isMultiServerMode ??= (count($this->getManager()->getServersNames()) > 1);
    }

    /**
     * Get List of Available Servers
     *
     * @return array
     */
    protected function getServersNames(): array
    {
        return $this->getManager()->getServersNames();
    }

    /**
     * Set Connector Manager
     *
     * @return ConnectorsManager
     */
    private function getManager(): ConnectorsManager
    {
        return $this->manager;
    }
}
