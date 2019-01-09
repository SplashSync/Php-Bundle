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

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Events\IdentifyServerEvent;

/**
 * @abstract    Identify Events Manager for Spash Connectors
 */
trait IdentifyEventsTrait
{
    /**
     * @abstract    Identify Connector Server Using Webservice Id
     *
     * @param IdentifyServerEvent $event
     *
     * @return bool
     */
    public function onIdentifyEvent(IdentifyServerEvent $event)
    {
        //====================================================================//
        //  Identify Server & Configure Connector
        $serverId   =   $this->identify($event->getWebserviceId());
        
        //====================================================================//
        //  If Server Found => Configure Connector Service
        if ($serverId) {
            //====================================================================//
            //  Safety Check => Same Connector Service Name
            $profile = $event->getConnector()->getProfile();
            if (isset($profile["name"]) && ($this->getConnectorName($serverId) == $profile["name"])) {
                $event->configure($this->getServerConfiguration($serverId));
            }
        }
//        //====================================================================//
//        // Debug Propose Only
//        $event->setRejected();
        //====================================================================//
        // Stop Event Propagation
        $event->stopPropagation();
    }
}
