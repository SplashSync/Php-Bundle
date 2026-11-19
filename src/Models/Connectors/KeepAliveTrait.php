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

namespace Splash\Bundle\Models\Connectors;

use Splash\Bundle\Events\ConnectorKeepAliveEvent;

/**
 * Connector Functions for Long Running Actions
 *
 * Lets a connector tell the host it is still alive and needs more time.
 */
trait KeepAliveTrait
{
    /**
     * Timestamp of the last keep-alive event dispatched by this connector
     *
     * @var float
     */
    private float $lastKeepAlive = 0.0;

    /**
     * Tell the Host this Connector is Still Alive and Needs More Time.
     *
     * Call it freely inside long loops (pagination, large imports): the
     * event is dispatched at most once per ConnectorKeepAliveEvent::INTERVAL,
     * the calls in between are dropped.
     *
     * @param null|string $message Optional free text for logs (i.e. "Import page 12/40")
     *
     * @return void
     */
    public function keepAlive(?string $message = null): void
    {
        $now = microtime(true);
        if (($now - $this->lastKeepAlive) < ConnectorKeepAliveEvent::INTERVAL) {
            return;
        }
        $this->lastKeepAlive = $now;
        $this->getEventDispatcher()->dispatch(
            new ConnectorKeepAliveEvent($this->getWebserviceId(), $message)
        );
    }
}
