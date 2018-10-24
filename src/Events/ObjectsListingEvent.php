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
 * Standalone Object Listing Event
 * This Event is Triggered by Standalone Connector to List Available Local Objects Types
 */
class ObjectsListingEvent extends Event
{
    
    /**
     * Event Name
     */
    const NAME  =   "splash.standalone.list.objects";

    /**
     * @var array
     */
    protected $objects = array();

    /**
     * @abstract    Add an Object Type to Standalone Connector
     * @param string $ObjectType
     * @param string $Service
     * @return void
     */
    public function addObjectType(string $ObjectType, string $Service) : void
    {
        $this->objects[$ObjectType] = array(
            "ObjectType"    =>  $ObjectType,
            "Service"       =>  $Service,
        );
    }
    
    /**
     * @abstract    Generate Array of Objects Types Names
     * @return array
     */
    public function getObjectTypes()
    {
        return array_keys($this->objects);
    }
    
    /**
     * @abstract    Get Service Namùe for an Object Type
     * @param   string $ObjectType
     * @return  string|null
     */
    public function getServiceName(string $ObjectType)
    {
        if (!isset($this->objects[$ObjectType]) || empty($this->objects[$ObjectType]["Service"])) {
            return null;
        }
        return $this->objects[$ObjectType]["Service"];
    }
}
