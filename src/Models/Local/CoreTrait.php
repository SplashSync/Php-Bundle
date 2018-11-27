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

use ArrayObject;
use Splash\Core\SplashCore as Splash;

/**
 * @abstract    Splash Bundle Local Class Core Functions
 */
trait CoreTrait
{
    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function parameters()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        $parameters       =     array();
        //====================================================================//
        // Safety Check - Server Identify Already Selected
        if (!$this->getServerId()) {
            return $parameters;
        }
        //====================================================================//
        // Server Identification Parameters
        $parameters["WsIdentifier"]         =   $this->getWebserviceId();
        $parameters["WsEncryptionKey"]      =   $this->getWebserviceKey();
        //====================================================================//
        // If Expert Mode => Overide of Server Host Address
        if (!empty($this->getWebserviceHost())) {
            $parameters["WsHost"]           =   $this->getWebserviceHost();
        }
        //====================================================================//
        // Use of Symfony Routes => Overide of Local Server Path Address
        $parameters["ServerPath"]      =   $this->getServerPath();

        return $parameters;
    }
    
    /**
     * {@inheritdoc}
     */
    public function includes()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);

        return true;
    }
           
    /**
     * {@inheritdoc}
     */
    public function selfTest()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        //  Load Local Translation File
        Splash::Translator()->Load("main@local");
        //====================================================================//
        //  Verify - Server Identifier Given
        if (empty($this->getWebserviceId())) {
            return Splash::log()->err("ErrSelfTestNoWsId");
        }
        //====================================================================//
        //  Verify - Server Encrypt Key Given
        if (empty($this->getWebserviceKey())) {
            return Splash::log()->err("ErrSelfTestNoWsKey");
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function informations($informations)
    {
        return $this->getConnector()->informations($informations);
    }
}
