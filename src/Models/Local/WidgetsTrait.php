<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Local;

use Exception;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Widgets\Manager;
use Splash\Models\Widgets\WidgetInterface;

/**
 * Splash Bundle Local Class Widgets Functions
 */
trait WidgetsTrait
{
    /**
     * @var array
     */
    private array $widgetManagers = array();

    /**
     * {@inheritdoc}
     */
    public function widgets(): array
    {
        //====================================================================//
        // Load Widgets Type List
        try {
            return $this->getConnector()->getAvailableWidgets();
        } catch (Exception $ex) {
            Splash::log()->report($ex);

            return array();
        }
    }

    /**
     * {@inheritdoc}
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
