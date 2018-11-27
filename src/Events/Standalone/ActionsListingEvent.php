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

namespace Splash\Bundle\Events\Standalone;

use Symfony\Component\EventDispatcher\Event;

/**
 * Standalone Actions Listing Event
 * This Event is Triggered by Standalone Connector to List Available Actions for Local Connectors
 */
class ActionsListingEvent extends Event
{
    /**
     * Event Name
     */
    const NAME  =   "splash.standalone.list.actions";

    /**
     * @var array
     */
    protected $actions = array();

    /**
     * @var array
     */
    protected $defaults = array();

    
    /**
     * @abstract    Add an Controller Action to Standalone Connector
     *
     * @param   string $code    Action Unique Code (MyConnectorAction)
     * @param   string $action  Symfony Controller Action (MyBundle:MyController:MyAction)
     * @param   array  $default Symfony Controller Defaults Parameters
     *
     * @return  void
     */
    public function addAction(string $code, string $action, array $default = array()) : void
    {
        $this->actions[strtolower($code)]   = $action;
        $this->defaults[strtolower($code)]  = $default;
    }
    
    /**
     * @abstract    Check if Action Code Exists
     *
     * @param   string $code Action Unique Code (MyConnectorAction)
     *
     * @return  bool
     */
    public function has(string $code)
    {
        return isset($this->actions[strtolower($code)]) && !empty($this->actions[strtolower($code)]);
    }
    
    /**
     * @abstract    Get Controller Action
     *
     * @param   string $code Action Unique Code (MyConnectorAction)
     *
     * @return  string|null
     */
    public function get(string $code)
    {
        if (!$this->has($code)) {
            return null;
        }

        return $this->actions[strtolower($code)];
    }

    /**
     * @abstract    Get Controller Action Defaults Parameters
     *
     * @param   string $code Action Unique Code (MyConnectorAction)
     *
     * @return  array|null
     */
    public function getDefault(string $code)
    {
        if (!$this->has($code)) {
            return null;
        }

        return $this->defaults[strtolower($code)];
    }
    
    /**
     * @abstract    Get All Controller Actions
     *
     * @return  array
     */
    public function getAll() : array
    {
        return $this->actions;
    }
}
