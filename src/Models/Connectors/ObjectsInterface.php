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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Splash\Models\AbstractObject;


/**
 * @abstract Define Required structure for Connectors Objects Access 
 */
interface ObjectsInterface {
    
    /**
     * @abstract    Fetch Server Available Objects List 
     * 
     * @return     ArrayObject|bool
     */    
    public function Objects();
    
    /**
     * @abstract   Get Server Objects Local Class 
     * 
     * @param   string  $ObjectType         Remote Object Type NAme. 
     * 
     * @return  AbstractObject
     * @throws  NotFoundHttpException
     */    
    public function Object( string $ObjectType ) : AbstractObject;
    
}
