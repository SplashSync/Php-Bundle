<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
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
     * @var array
     */
    private $widgetManagers = array();

    /**
     * @abstract   Build list of Available Widgets
     *
     * @return array
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
     * @param string $widgetType Specify Widgets Type Name
     *
     * @return WidgetInterface
     */
    public function widget(string $widgetType): WidgetInterface
    {
        //====================================================================//
        // Build Widgets Type Index Key
        $index = get_class($this->getConnector())."::".$widgetType;

        //====================================================================//
        // If Widgets Manager is New
        if (!isset($this->widgetManagers[$index])) {
            $this->widgetManagers[$index] = new Manager($this->getConnector(), $widgetType);
        }

        //====================================================================//
        // Return Widgets Manager
        return $this->widgetManagers[$index];
    }
}
