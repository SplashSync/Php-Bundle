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
 **/


namespace Splash\Bundle\Models\Connectors;

use ArrayObject;

/**
 * @abstract Define Required structure for Administration of Communication Connectors 
 */
interface AdminInterface {
    
    /**
     * @abstract   Minimal Test of WebService connection. No Encryption, Just Verify Remote Server is found
     * 
     * @return  bool
     */    
    public function ping() : bool;

    /**
     * @abstract   Connect WebService and fetch server informations 
     * 
     * @return  bool
     */    
    public function connect() : bool;
    
    /**
     * @abstract   Fetch Server Informations 
     * @param   ArrayObject  $Informations   Informations Inputs
     * @return  ArrayObject
     */    
    public function informations(ArrayObject  $Informations) : ArrayObject;
    
    /**
     * @abstract   Fetch Server Parameters
     * @return  array
     */    
    public function parameters() : array;
    
    /**
     * @abstract   Fetch Server Self Test Results 
     * 
     * @return  bool
     */    
    public function selfTest() : bool;    
    
}
