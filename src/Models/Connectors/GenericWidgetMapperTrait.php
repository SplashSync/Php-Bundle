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

namespace Splash\Bundle\Models\Connectors;

use Exception;
use Splash\Bundle\Models\AbstractStandaloneWidget;
use Splash\Models\AbstractWidget;

/**
 * Manager Access to Generic Splash Widgets for Stantard Connectors
 *
 * Connector Only Map Widgets Type => Classname and Mapper will do the rest
 *
 * Widget must extend Splash\Models\AbstractWidget to be used
 * Widget that extend AbstractStandaloneWidget will be Configured before use
 *
 * Map is defined on STATIC variable $widgetsMap
 */
trait GenericWidgetMapperTrait
{
    /**
     * {@inheritdoc}
     */
    public function getAvailableWidgets() : array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return array();
        }
        //====================================================================//
        // Remove Disabled Objects
        foreach (static::$widgetsMap as $widgetType => $widgetClass) {
            if ($widgetClass::isDisabled()) {
                unset(static::$widgetsMap[$widgetType]);
            }
        }

        //====================================================================//
        // Get Generic Widgets Types List
        return array_keys(static::$widgetsMap ?? array());
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDescription(string $widgetType) : array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return array();
        }

        //====================================================================//
        // Get Generic Widget Type Description
        return $this->getWidgetLocalClass($widgetType)->description();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetContents(string $widgetType, array $widgetConfig = array()): ?array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return null;
        }

        //====================================================================//
        // Get Generic Widget Fields List
        return $this->getWidgetLocalClass($widgetType)->get($widgetConfig);
    }

    /**
     * Return a New Instance of Requested Widget Type Class
     *
     * @param string $widgetType
     *
     * @throws Exception
     *
     * @return AbstractWidget
     */
    private function getWidgetLocalClass(string $widgetType) : AbstractWidget
    {
        //====================================================================//
        // Safety Check => Widget Type is Mapped
        if (!in_array($widgetType, array_keys(self::$widgetsMap), true)) {
            throw new Exception(sprintf("Unknown Widget Type : %s", $widgetType));
        }
        //====================================================================//
        // Get Widget Class
        $className = static::$widgetsMap[$widgetType];
        //====================================================================//
        // Safety Check => Validate Widget Class
        if ($this->isValidWidgetClass($className)) {
            throw new Exception($this->isValidWidgetClass($className));
        }
        //====================================================================//
        // Create Widget Class
        $genericWidget = new $className($this);
        //====================================================================//
        // If StandaloneWidget => Configure it!
        if (is_subclass_of($className, AbstractStandaloneWidget::class)) {
            $genericWidget->configure($widgetType, $this->getWebserviceId(), $this->getConfiguration());
        }

        return $genericWidget;
    }

    /**
     * Validate Widget Class Name
     *
     * @param mixed $className
     *
     * @return null|string
     */
    private function isValidWidgetClass($className): ?string
    {
        //====================================================================//
        // Safety Check => Widget Type is String
        if (!is_string($className)) {
            return "Widget Type is Not a String";
        }
        //====================================================================//
        // Safety Check => Class Exists
        if (!class_exists($className)) {
            return "Widget Class Not Found";
        }
        //====================================================================//
        // Safety Check => Class Extends
        if (!is_subclass_of($className, AbstractWidget::class)) {
            return "Widget Class MUST extends ".AbstractWidget::class;
        }

        return null;
    }
}
