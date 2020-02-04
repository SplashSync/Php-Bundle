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

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Connectors\NullConnector;
use Splash\Bundle\Events\IdentifyServerEvent;

/**
 * Identify Events Manager for Spash Connectors
 */
trait IdentifyEventsTrait
{
    /**
     * Identify Connector Server Using Webservice Id
     *
     * @param IdentifyServerEvent $event
     *
     * @return void
     */
    public function onIdentifyEvent(IdentifyServerEvent $event): void
    {
        //====================================================================//
        //  Identify Server & Configure Connector
        $webserviceId = $this->identify($event->getWebserviceId());
        //====================================================================//
        //  If Server Found => Configure Connector Service
        if ($webserviceId) {
            $this->configureIdentifyEvent($event, $webserviceId);
        }
        //====================================================================//
        // Stop Event Propagation
        $event->stopPropagation();
    }

    /**
     * Configure Identify Event Using Webservice Id
     *
     * @param IdentifyServerEvent $event
     * @param string              $webserviceId
     *
     * @return void
     */
    private function configureIdentifyEvent(IdentifyServerEvent $event, string $webserviceId): void
    {
        //====================================================================//
        //  If Event is Null Connector => Setup Connector Service
        if ($event->getConnector() instanceof NullConnector) {
            $newConnector = $this->get($webserviceId);
            if ($newConnector) {
                $event->setConnector($newConnector);
            }
        }
        //====================================================================//
        //  Safety Check => Same Connector Service Name
        $profile = $event->getConnector()->getProfile();
        if (isset($profile["name"]) && ($this->getConnectorName($webserviceId) == $profile["name"])) {
            $event->configure($this->getServerConfiguration($webserviceId));
        }
    }
}
