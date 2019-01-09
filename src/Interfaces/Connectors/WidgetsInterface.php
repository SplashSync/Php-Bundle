<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;

/**
 * @abstract Define Required structure for Connectors Widgets Access
 */
interface WidgetsInterface
{
    /**
     * @abstract   Fetch Server Available Widgets List
     *
     * @return array
     */
    public function getAvailableWidgets() : array;
    
    /**
     * @abstract   Read Widget Definition
     *
     * @param string $widgetType Widgets Type Name
     *
     * @return array
     */
    public function getWidgetDescription(string $widgetType) : array;
    
    /**
     * @abstract   Read Widget Contents
     *
     * @param string $widgetType Widgets Type Name
     * @param array  $params     Widget Rendering Parameters
     *
     * @return array|false
     */
    public function getWidgetContents(string $widgetType, array $params = array());
}
