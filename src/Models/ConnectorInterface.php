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

use Splash\Bundle\Models\Connectors\AdminInterface;
use Splash\Bundle\Models\Connectors\ObjectsInterface;
use Splash\Bundle\Models\Connectors\WidgetsInterface;
use Splash\Bundle\Models\Connectors\FilesInterface;
use Splash\Bundle\Models\Connectors\ProfileInterface;

/**
 * @abstract Define Required structure for Communication Connectors 
 */
interface ConnectorInterface extends 
        AdminInterface, 
        ObjectsInterface, 
//        WidgetsInterface, 
//        FilesInterface,
        ProfileInterface 
{
    //  Enable Connectors Transaction Debugging
    const ENABLE_DEBUG      = False;            

}
