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
 * Connector Keep-Alive Event
 *
 * Triggered by a Connector running a long action: it is still alive and
 * needs more time. The host (bridge, worker, tasking) relays the signal
 * to whoever watches the call deadline. This package only emits it.
 */
class ConnectorKeepAliveEvent extends Event
{
    /**
     * Event Name
     */
    const NAME = "Splash\\Bundle\\Events\\ConnectorKeepAliveEvent";

    /**
     * Minimum delay between two events of the same connector, in seconds
     *
     * Consumers may rely on it: a connector calling keepAlive() in a tight
     * loop dispatches at most one event per interval.
     */
    const INTERVAL = 10;

    /**
     * WebService ID Of Impacted Server
     *
     * @var string
     */
    private string $webserviceId;

    /**
     * Optional Free Text for Logs (i.e. "Import page 12/40")
     *
     * @var null|string
     */
    private ?string $message;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param string      $webserviceId WebService ID
     * @param null|string $message      Optional Free Text for Logs
     */
    public function __construct(string $webserviceId, ?string $message = null)
    {
        //==============================================================================
        //      Data Storages
        $this->webserviceId = $webserviceId;
        $this->message = $message;
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
     * Get Message
     *
     * @return null|string
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
}
