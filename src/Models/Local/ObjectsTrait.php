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

use Splash\Local\Objects\Manager;

use Splash\Models\Objects\ObjectInterface;

/**
 * @abstract    Splash Bundle Local Class Objects Functionss
 */
trait ObjectsTrait
{
    //====================================================================//
    // *******************************************************************//
    //  OVERRIDING CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     * @abstract   Build list of Available Objects
     * @return     array
     */
    public function objects()
    {
        //====================================================================//
        // Load Objects Type List
        return $this->getConnector()->getAvailableObjects();
    }

    /**
     * @abstract   Get Specific Object Class
     *             This function is a router for all local object classes & functions
     *
     * @params     $type       Specify Object Class Name
     *
     * @return     ObjectInterface
     */
    public function object($ObjectType = null) : ObjectInterface
    {
        return new Manager($this->getConnector(), $ObjectType);
    }
}
