<?php
/*
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
 */

/**
 * @abstract    Local Overriding Objects Manager for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Widgets;

use Splash\Models\Widgets\WidgetInterface;

use Splash\Bundle\Interfaces\ConnectorInterface;

/**
 * @abstract    Splash Bundle Connectors Widgets Access
 */
class Manager implements WidgetInterface
{
    /**
     * @var ConnectorInterface
     */
    private $Connector      = null;
    
    /**
     * @var string
     */
    private $WidgetType     = null;
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     * @abstract       Init a New Widget Manager
     * @param   ConnectorInterface  $Connector
     * @param   string              $WidgetType
     * @return  void
     */
    public function __construct(ConnectorInterface $Connector, string $WidgetType)
    {
        $this->Connector    =   $Connector;
        $this->WidgetType   =   $WidgetType;
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
        return $this->Connector->getWidgetDescription($this->WidgetType);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($Parameters = array())
    {
        //====================================================================//
        // Forward Action
        return $this->Connector->getWidgetContents($this->WidgetType, $Parameters);
    }
}
