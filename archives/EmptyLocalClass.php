<?php
/*
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
 */

namespace Connectors\CoreBundle\Models;

use ArrayObject;
use Connectors\CoreBundle\Models\ConnectorInterface;
use Splash\Models\AbstractObject;

/**
 * @abstract    Empty Local Overiding Objects Manager for Splash Connectors
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class EmptyLocalClass
{

    /**
     * @var ConnectorInterface
     */
    private $Connector = null;
    
    /**
     * @param ConnectorInterface $Connector
     */
    public function __construct(ConnectorInterface $Connector = null)
    {
        $this->Connector    =   $Connector;
    }
    
    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     *      @abstract       Return Local Server Parameters as Aarray
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called on each initialisation of the module
     *
     *      Result must be an array including mandatory parameters as strings
     *         ["WsIdentifier"]         =>>  Name of Module Default Language
     *         ["WsEncryptionKey"]      =>>  Name of Module Default Language
     *         ["DefaultLanguage"]      =>>  Name of Module Default Language
     *
     *      @return         array       $parameters
     */
    public function Parameters()
    {
        return array();
    }
    
    /**
     *      @abstract       Include Local Includes Files
     *
     *      Include here any local files required by local functions.
     *      This Function is called each time the module is loaded
     *
     *      There may be differents scenarios depending if module is
     *      loaded as a library or as a NuSOAP Server.
     *
     *      This is triggered by global constant SPLASH_SERVER_MODE.
     *
     *      @return         bool
     */
    public function Includes()
    {
        return true;
    }

    /**
     *      @abstract       Return Local Server Self Test Result
     *
     *      THIS FUNCTION IS MANDATORY
     *
     *      This function called during Server Validation Process
     *
     *      We recommand using this function to validate all functions or parameters
     *      that may be required by Objects, Widgets or any other modul specific action.
     *
     *      Use Module Logging system & translation tools to retrun test results Logs
     *
     *      @return         bool    global test result
     */
    public function SelfTest()
    {
        return true;
    }
    
    /**
     *  @abstract   Update Server Informations with local Data
     *
     *  @param     ArrayObject $Informations Informations Inputs
     *
     *  @return     ArrayObject
     */
    public function Informations($Informations)
    {
        return $Informations;
    }
    
    //====================================================================//
    // *******************************************************************//
    //  OVERRIDING CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     * @abstract   Build list of Available Objects
     *
     * @return     array       $list           list array including all available Objects Type
     */
    public function Objects()
    {
        if ($this->Connector) {
            return $this->Connector
                    ->getAvailableObjects($this->Connector->getNode())
                    ->getArrayCopy();
        }

        return array();
    }
    
    /**
     * @abstract   Get Local Object Class
     *
     * @return     AbstractObject
     */
    public function Object($ObjectType)
    {
        if ($this->Connector) {
            return $this->Connector->getObjectLocalClass($ObjectType);
        }

        return null;
    }
    
    /**
     *      @abstract   Build list of Available Widgets
     *
     *      @return     array       $list           list array including all available Widgets Type
     */
    public function Widgets()
    {
        return array();
    }
}
