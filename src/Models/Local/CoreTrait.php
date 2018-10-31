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
        $Parameters       =     array();
        //====================================================================//
        // Safety Check - Server Identify Already Selected
        if (!$this->getServerId()) {
            return $Parameters;
        }
        //====================================================================//
        // Server Identification Parameters
        $Parameters["WsIdentifier"]         =   $this->getWebserviceId();
        $Parameters["WsEncryptionKey"]      =   $this->getWebserviceKey();
        //====================================================================//
        // If Expert Mode => Overide of Server Host Address
        if (!empty($this->getWebserviceHost())) {
            $Parameters["WsHost"]           =   $this->getWebserviceHost();
        }
        //====================================================================//
        // Use of Symfony Routes => Overide of Local Server Path Address
        $Parameters["ServerPath"]      =   $this->getServerPath();
        return $Parameters;
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
    public function informations($Informations)
    {
        return $this->getConnector()->informations($Informations);
    }
}
