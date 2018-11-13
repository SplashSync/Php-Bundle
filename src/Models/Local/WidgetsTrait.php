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

use Splash\Local\Widgets\Manager;

use Splash\Models\Widgets\WidgetInterface;

/**
 * @abstract    Splash Bundle Local Class Widgets Functionss
 */
trait WidgetsTrait
{
    /**
     * @abstract   Build list of Available Widgets
     *
     * @return     array
     */
    public function widgets()
    {
        //====================================================================//
        // Load Widgets Type List
        return $this->getConnector()->getAvailableWidgets();
    }

    /**
     * @abstract   Get Specific Widgets Class
     *             This function is a router for all local Widgets classes & functions
     *
     * @params     string $WidgetType       Specify Widgets Type Name
     *
     * @return     WidgetInterface
     */
    public function widget(string $WidgetType) : WidgetInterface
    {
        return new Manager($this->getConnector(), $WidgetType);
    }
}