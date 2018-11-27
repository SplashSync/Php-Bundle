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

namespace Splash\Bundle\Models\Manager;

use Exception;
use Splash\Bundle\Models\AbstractConnector as Connector;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;

/**
 * @abstract    Splash Connector Services Management
 */
trait ConnectorsTrait
{
    /**
     * Splash Connectors Service Array
     * @var array
     */
    private $connectors;
    
    /**
     * @abstract    Add a Connector Service to Manager
     *
     * @param   Connector $connectorService
     *
     * @return  $this
     */
    public function registerConnectorService(Connector $connectorService)
    {
        //====================================================================//
        // Read Connector Profile
        $profile    =   $connectorService->getProfile();
        //====================================================================//
        // Safety Check - Connector Provide a Name
        if (!isset($profile["connector"]) || empty($profile["connector"])) {
            throw new Exception("Connector Service Must provide its name in Profile Array['connector'].");
        }
        //====================================================================//
        // Safety Check - Connector Name is Unique
        if ($this->has($profile["connector"])) {
            throw new Exception("Connector Service Name Must be Unique.");
        }
        //====================================================================//
        // Register Connector
        $this->connectors[$profile["connector"]]    =   $connectorService;

        return $this;
    }
    
    /**
     * @abstract    Check if Connector Exists
     *
     * @param   string $connectorId
     *
     * @return  bool
     */
    public function has(string $connectorId) : bool
    {
        return isset($this->connectors[$connectorId]);
    }
    
    /**
     * @abstract    Get Connector Service & Pass Configuration for a Specified Server
     *
     * @param   string $serverId      Server Id or Splash Webservice Id
     * @param   array  $configuration
     *
     * @return  null|Connector
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function get(string $serverId, array $configuration = array())
    {
        //====================================================================//
        // Identify Requested Connection by Webservice Id
        if ($this->hasWebserviceConfiguration($serverId)) {
            $serverId   =   $this->hasWebserviceConfiguration($serverId);
        }
        //====================================================================//
        // Safety Check - Server Id Exists
        if (!$serverId || !$this->hasServerConfiguration($serverId)) {
            return null;
        }
        //====================================================================//
        // Safety Check - Connector Service Found
        if (!isset($this->connectors[$this->getConnectorName($serverId)])) {
            return null;
        }
        //====================================================================//
        // Load Connector Service
        $connector      =   $this->connectors[$this->getConnectorName($serverId)];
        //====================================================================//
        // Setup Connector Configuration
        $connector->configure(
            $this->getWebserviceId($serverId),
            array_replace_recursive($this->getServerConfiguration($serverId), $configuration)
        );
        //====================================================================//
        // Return Connector
        return $connector;
    }
    
    /**
     * @abstract    Identify Connector Service for a Specified WebService Id
     *
     * @param   string $webserviceId Splash WebService Id
     *
     * @return  null|Connector
     */
    public function identify(string $webserviceId)
    {
        //====================================================================//
        // Seach for This Connection in Local Configuration
        $serverId   =   $this->hasWebserviceConfiguration($webserviceId);
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$serverId) {
            return null;
        }
        //====================================================================//
        // Setup Splash Local Class
        $local  =   Splash::local();
        if ($local instanceof Local) {
            $local->setServerId($serverId);
        }
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
        //====================================================================//
        // Return ServerId
        return $serverId;
    }
}
