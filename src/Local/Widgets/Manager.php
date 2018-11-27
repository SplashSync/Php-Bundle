<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * @abstract    Local Overriding Objects Manager for Splash Bundle
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Widgets;

use Splash\Bundle\Interfaces\ConnectorInterface;
use Splash\Models\Widgets\WidgetInterface;

/**
 * @abstract    Splash Bundle Connectors Widgets Access
 */
class Manager implements WidgetInterface
{
    /**
     * @var ConnectorInterface
     */
    private $connector;
    
    /**
     * @var string
     */
    private $widgetType;
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     * @abstract       Init a New Widget Manager
     *
     * @param   ConnectorInterface $connector
     * @param   string             $widgetType
     *
     * @return  void
     */
    public function __construct(ConnectorInterface $connector, string $widgetType)
    {
        $this->connector    =   $connector;
        $this->widgetType   =   $widgetType;
    }
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function description()
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getWidgetDescription($this->widgetType);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($parameters = array())
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getWidgetContents($this->widgetType, $parameters);
    }
}
