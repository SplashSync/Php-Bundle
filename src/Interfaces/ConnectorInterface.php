<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Interfaces;

use Splash\Bundle\Interfaces\Connectors\AdminInterface;
use Splash\Bundle\Interfaces\Connectors\ConfigurationInterface;
use Splash\Bundle\Interfaces\Connectors\FilesInterface;
use Splash\Bundle\Interfaces\Connectors\ObjectsInterface;
use Splash\Bundle\Interfaces\Connectors\ProfileInterface;
use Splash\Bundle\Interfaces\Connectors\WidgetsInterface;

/**
 * @abstract Define Required structure for Communication Connectors
 */
interface ConnectorInterface extends
    ConfigurationInterface,
    AdminInterface,
    ObjectsInterface,
    WidgetsInterface,
    FilesInterface,
    ProfileInterface
{
    //  Enable Connectors Transaction Debugging
    const ENABLE_DEBUG = false;
}
