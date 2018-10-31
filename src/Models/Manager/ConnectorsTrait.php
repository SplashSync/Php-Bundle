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

use Exception;

use Splash\Bundle\Interfaces\ConnectorInterface as Connector;

/**
 * @abstract    Splash Connector Services Management
 */
trait ConnectorsTrait
{
    
    /**
     * Splash Connectors Service Array
     * @var array
     */
    private $Connectors;
    
    /**
     * @abstract    Add a Connector Service to Manager
     *
     * @param   Connector   $ConnectorService
     *
     * @return  $this
     */
    public function registerConnectorService(Connector $ConnectorService)
    {
        //====================================================================//
        // Read Connector Profile
        $Profile    =   $ConnectorService->getProfile();
        //====================================================================//
        // Safety Check - Connector Provide a Name
        if (!isset($Profile["connector"]) || empty($Profile["connector"])) {
            throw new Exception("Connector Service Must provide its name in Profile Array['connector'].");
        }
        //====================================================================//
        // Safety Check - Connector Name is Unique
        if ($this->has($Profile["connector"])) {
            throw new Exception("Connector Service Name Must be Unique.");
        }
        //====================================================================//
        // Register Connector
        $this->Connectors[$Profile["connector"]]    =   $ConnectorService;
        return $this;
    }
    
    /**
     * @abstract    Check if Connector Exists
     * @param   string      $ConnectorId
     * @return  bool
     */
    public function has(string $ConnectorId) : bool
    {
        return isset($this->Connectors[$ConnectorId]);
    }
    
    /**
     * @abstract    Get Connector Service & Pass Configuration for a Specified Server
     *
     * @param   string      $ServerId        Server Id or Splash Webservice Id
     * @param   array       $Configuration
     *
     * @return  Connector|null
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function get(string $ServerId, array $Configuration = array())
    {
        //====================================================================//
        // Identify Requested Connection by Webservice Id
        if ($this->hasWebserviceConfiguration($ServerId)) {
            $ServerId   =   $this->hasWebserviceConfiguration($ServerId);
        }
        //====================================================================//
        // Safety Check - Server Id Exists
        if (!$this->hasServerConfiguration($ServerId)) {
            return null;
        }
        //====================================================================//
        // Safety Check - Connector Service Found
        if (!isset($this->Connectors[$this->getConnectorName($ServerId)])) {
            return null;
        }
        //====================================================================//
        // Load Connector Service
        $Connector      =   $this->Connectors[$this->getConnectorName($ServerId)];
        //====================================================================//
        // Setup Connector Configuration
        $Connector->configure(
                $this->getWebserviceId($ServerId), 
                array_merge_recursive($this->getServerConfiguration($ServerId), $Configuration)
            );
        //====================================================================//
        // Return Connector
        return $Connector;
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
        $ServerId   =   $this->hasWebserviceConfiguration($WebserviceId);
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$ServerId) {
            return null;
        }
        $this->setCurrent($ServerId);
        //====================================================================//
        // Return Connector
        return $this->get($ServerId);
    }
}
