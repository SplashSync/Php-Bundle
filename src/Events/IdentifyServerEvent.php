<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Events;

use Splash\Bundle\Connectors\NullConnector;
use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\EventDispatcher\Event;

/**
 * Connectors Object Commit Event
 *
 * This Event is Triggered by Any Connector to Submit Objects Changes to Server.
 */
class IdentifyServerEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'Splash\Bundle\Events\IdentifyServerEvent';

    /**
     * Current Connector fo Identify
     *
     * @var AbstractConnector
     */
    private $connector;

    /**
     * WebService Id Of Server To Identify
     *
     * @var string
     */
    private $webserviceId;

    /**
     * Server Was Identified
     *
     * @var bool
     */
    private $identified = false;

    /**
     * Server Connection Rejected
     *
     * @var bool
     */
    private $rejected = false;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param AbstractConnector $connector
     * @param string            $webserviceId
     */
    public function __construct(AbstractConnector $connector, string  $webserviceId)
    {
        //==============================================================================
        //      Basic Data Strorages
        $this->connector = $connector;
        $this->webserviceId = $webserviceId;
    }

    /**
     * Configure Connector
     *
     * @param array $configuration
     *
     * @return bool
     */
    public function configure(array $configuration) : bool
    {
        //====================================================================//
        // Setup Connector Configuration
        $this->getConnector()->configure(
            $this->getWebserviceId(),
            $this->getWebserviceId(),
            $configuration
        );
        //====================================================================//
        // Mark Connector as Configured
        return $this->identified = true;
    }

    /**
     * Refuse Connection for this Server
     *
     * @return self
     */
    public function setRejected()
    {
        $this->rejected = true;

        return $this;
    }

    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================

    /**
     * Set Connector
     *
     * Update Connector Allowed Only when Event was Created with NullConnector
     * OtherwizeConnector Service MUST remain the same
     *
     * @param AbstractConnector $connector
     *
     * @return $this
     */
    public function setConnector(AbstractConnector $connector): self
    {
        if ($this->connector instanceof NullConnector) {
            $this->connector = $connector;
        }

        return $this;
    }

    /**
     * Get Connector
     *
     * @return AbstractConnector
     */
    public function getConnector(): AbstractConnector
    {
        return $this->connector;
    }

    /**
     * Get Webservice Id
     *
     * @return string
     */
    public function getWebserviceId(): string
    {
        return $this->webserviceId;
    }

    /**
     * Server was Identified
     *
     * @return bool
     */
    public function isIdentified(): bool
    {
        return $this->identified;
    }

    /**
     * Server Connection was Rejected
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->rejected;
    }
}
