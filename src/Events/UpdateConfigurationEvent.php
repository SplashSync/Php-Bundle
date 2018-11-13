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
 * @abstract    Update Connectors Configuration Event
 * This Event is Triggered by Any Connector Ask for Update of Server Configuration in Memory
 */
class UpdateConfigurationEvent extends Event
{

    /**
     * Event Name
     */
    const NAME  =   "splash.connectors.update";

    /**
     * @abstract    WebService Id Of Impacted Server
     * @var string
     */
    private $WebserviceId = null;

    /**
     * @abstract    New Configuration for this Server
     * @var array
     */
    private $Configuration = null;
    
    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
     *
     * @param string                    $WebserviceId
     * @param array                     $Configuration
     *
     * @return void
     */
    public function __construct(string  $WebserviceId, array $Configuration) {
        //==============================================================================
        //      Data Storages
        $this->WebserviceId     =   $WebserviceId;
        $this->Configuration    =   $Configuration;
    }
    
    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================
        
    /**
     * @abstract    Get WebService Id
     * @return  string
     */
    public function getWebserviceId() : string
    {
        return $this->WebserviceId;
    }

    /**
     * @abstract    Get Configuration
     * @return  array
     */
    public function getConfiguration() : array
    {
        return $this->Configuration;
    }

}
