<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Events;

use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\EventDispatcher\Event;

/**
 * @abstract    Connectors Object Commit Event
 * This Event is Triggered by Any Connector to Submit Objects Changes to Server.
 */
class IdentifyServerEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'splash.connectors.identify';

    /**
     * @abstract    Current Connector fo Identify
     *
     * @var AbstractConnector
     */
    private $connector;

    /**
     * @abstract    WebService Id Of Server To Identify
     *
     * @var string
     */
    private $webserviceId;

    /**
     * @abstract    Server Was Identified
     *
     * @var bool
     */
    private $identified = false;

    /**
     * @abstract    Server Connection Rejected
     *
     * @var bool
     */
    private $rejected = false;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
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
     * @abstract    Configure Connector
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
     * @abstract    Refuse Connection for this Server
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
     * @abstract    Get Connector
     *
     * @return AbstractConnector
     */
    public function getConnector(): AbstractConnector
    {
        return $this->connector;
    }

    /**
     * @abstract    Get Webservice Id
     *
     * @return string
     */
    public function getWebserviceId(): string
    {
        return $this->webserviceId;
    }

    /**
     * @abstract    Server was Identified
     *
     * @return bool
     */
    public function isIdentified(): bool
    {
        return $this->identified;
    }

    /**
     * @abstract    Server Connection was Rejected
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->rejected;
    }
}
