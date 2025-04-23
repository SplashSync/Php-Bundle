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

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Events\ObjectsCommitEvent;
use Splash\Core\Client\Splash;

/**
 * Objects Events Manager for Splash Connectors
 */
trait ObjectsEventsTrait
{
    /**
     * Propagate Commit to Splash Server Using Connector Webservice Infos
     *
     * @param ObjectsCommitEvent $event
     *
     * @return bool
     */
    public function onCommitEvent(ObjectsCommitEvent $event): bool
    {
        //====================================================================//
        //  Identify Server & Configure Connector
        $this->identify($event->getWebserviceId());

        //====================================================================//
        //  Submit Commit to Splash Server
        return Splash::commit(
            $event->getObjectType(),
            $event->getObjectsIds(),
            $event->getAction(),
            $event->getUserName(),
            $event->getComment()
        );
    }
}
