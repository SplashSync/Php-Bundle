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


namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Splash\Models\WidgetBase;

/**
 * @abstract Define Required structure for Connectors Widgets Access
 */
interface WidgetsInterface
{
    
    /**
     * @abstract   Fetch Server Available Widgets List
     *
     * @return  ArrayObject
     */
    public function widgets() : ArrayObject;
    
    /**
     * @abstract   Get Widget Access Class
     *
     * @param   string  $WidgetType         Widgets Type Name
     *
     * @return  WidgetBase|bool
     * @throws  NotFoundHttpException
     */
    public function widget(string $WidgetType) : WidgetBase;
}
