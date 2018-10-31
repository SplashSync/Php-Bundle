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

namespace Splash\Bundle\Events;

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
     * @abstract    Add an Controller Action to Standalone Connector
     * @param   string  $Code       Action Unique Code (MyConnectorAction)
     * @param   string  $Action     Symfony Controller Action (MyBundle:MyController:MyAction)
     * @return  void
     */
    public function addAction(string $Code, string $Action) : void
    {
        $this->actions[strtolower($Code)] = $Action;
    }
    
    /**
     * @abstract    Check if Action Code Exists
     * @param   string  $Code       Action Unique Code (MyConnectorAction)
     * @return  bool
     */
    public function has(string $Code)
    {
        return isset($this->actions[strtolower($Code)]) && !empty($this->actions[strtolower($Code)]);
    }
    
    /**
     * @abstract    Get Controller Action
     * @param   string  $Code       Action Unique Code (MyConnectorAction)
     * @return  string|null
     */
    public function get(string $Code)
    {
        if (!$this->has($Code)) {
            return null;
        }
        return $this->actions[strtolower($Code)];
    }
    
    /**
     * @abstract    Get All Controller Actions
     * @return  array
     */
    public function getAll() : array
    {
        return $this->actions;
    }
}
