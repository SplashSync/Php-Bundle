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

namespace Splash\Bundle\Events;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Update Connectors Configuration Event
 * This Event is Triggered by Any Connector
 * Ask for Update of Server Configuration in Memory
 */
class UpdateConfigurationEvent extends Event
{
    /**
     * Event Name
     */
    const NAME = "Splash\\Bundle\\Events\\UpdateConfigurationEvent";

    /**
     * WebService ID Of Impacted Server
     *
     * @var string
     */
    private string $webserviceId;

    /**
     * New Configuration for this Server
     *
     * @var array
     */
    private array $configuration;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param string $webserviceId  WebService ID
     * @param array  $configuration New Configuration
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
     * Get WebService ID
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
