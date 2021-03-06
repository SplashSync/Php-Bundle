<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
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
    const NAME = "Splash\\Bundle\\Events\\UpdateConfigurationEvent";

    /**
     * WebService Id Of Impacted Server
     *
     * @var string
     */
    private $webserviceId;

    /**
     * New Configuration for this Server
     *
     * @var array
     */
    private $configuration;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param string $webserviceId
     * @param array  $configuration
     */
    public function __construct(string  $webserviceId, array $configuration)
    {
        //==============================================================================
        //      Data Storages
        $this->webserviceId = $webserviceId;
        $this->configuration = $configuration;
    }

    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================

    /**
     * Get WebService Id
     *
     * @return string
     */
    public function getWebserviceId() : string
    {
        return $this->webserviceId;
    }

    /**
     * Get Configuration
     *
     * @return array
     */
    public function getConfiguration() : array
    {
        return $this->configuration;
    }
}
