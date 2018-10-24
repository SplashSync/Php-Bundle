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

namespace Splash\Bundle\Models\Manager;

use Splash\Client\Splash;

use Splash\Bundle\Events\ObjectsCommitEvent;

/**
 * @abstract    Objects Events Manager for Spash Connectors
 */
trait ObjectsEventsTrait
{
    
    /**
     * @abstract    Propagate Commit to Spalsh Server Using Connector Webservice Infos
     * @param       ObjectsCommitEvent  $Event
     * @return      bool
     */
    public function onCommitEvent(ObjectsCommitEvent $Event)
    {
        //====================================================================//
        //  Identify Server & Configure Connector
        $this->identify($Event->getServerId());
        //====================================================================//
        //  Submit Commit to Splash Server
        return Splash::commit(
            $Event->getObjectType(),
            $Event->getObjectsIds(),
            $Event->getAction(),
            $Event->getUserName(),
            $Event->getComment()
        );
    }
}
