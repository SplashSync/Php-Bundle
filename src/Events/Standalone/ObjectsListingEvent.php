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

namespace Splash\Bundle\Events\Standalone;

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
     *
     * @param string $objectType
     * @param string $service
     *
     * @return void
     */
    public function addObjectType(string $objectType, string $service) : void
    {
        $this->objects[$objectType] = array(
            "ObjectType"    =>  $objectType,
            "Service"       =>  $service,
        );
    }
    
    /**
     * @abstract    Generate Array of Objects Types Names
     *
     * @return array
     */
    public function getObjectTypes()
    {
        return array_keys($this->objects);
    }
    
    /**
     * @abstract    Get Service Namùe for an Object Type
     *
     * @param   string $objectType
     *
     * @return  null|string
     */
    public function getServiceName(string $objectType)
    {
        if (!isset($this->objects[$objectType]) || empty($this->objects[$objectType]["Service"])) {
            return null;
        }

        return $this->objects[$objectType]["Service"];
    }
}
