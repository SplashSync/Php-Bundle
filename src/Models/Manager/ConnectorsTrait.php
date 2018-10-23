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

use Exeception;

use Splash\Bundle\Models\ConnectorInterface as Connector;

/**
 * @abstract    Splash Connector Services Management
 */
trait ConnectorsTrait {
    
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
    public function registerConnectorService(Connector $ConnectorService) {
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
    public  function has(string $ConnectorId) : bool 
    {
        return isset($this->Connectors[$ConnectorId]);
    }   
    
    /**
     * @abstract    Get Connector Service & Pass Configuration for a Specified Server
     * @param   string      $ConnectorId        Connector Service Id or Splash Server Id
     * @param   array       $Configuration
     * @return  Connector|null
     */
    public  function get(string $ConnectorId, array $Configuration = array()) 
    {

        //====================================================================//
        // Safety Check - Connector Exists        
        if (!$this->has($ConnectorId) && !$this->hasServerConfiguration($ConnectorId)) {
            return null;
        } 
        if ($this->has($ConnectorId)) {
            $Connector      =   $this->Connectors[$ConnectorId];
//            $BaseConfig     =   array(); 
        } else {
            $ConnectorName  =   $this->getConnectorName($ConnectorId);          
            $Connector      =   $this->Connectors[$ConnectorName];
//            $BaseConfig     =   $this->getServerConfiguration($ConnectorId); 
        }
//        //====================================================================//
//        // Setup Connector Configuration   
//        $Connector->setConfiguration(array_merge_recursive($BaseConfig, $Configuration));
        //====================================================================//
        // Return Connector
        return $Connector;
    } 
    
    /**
     * @abstract    Identify Connector Service for a Specified WebService Id
     * @param   string      $WebserviceId        Splash WebService Id
     * @return  Connector|null
     */
    public  function identify(string $WebserviceId) 
    {
        //====================================================================//
        // Seach for This Connection in Local Configuration       
        $ServerId   =   $this->hasServerIdConfiguration($WebserviceId);
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
