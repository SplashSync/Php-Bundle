<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
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
use Splash\Client\Splash;
use Splash\Local\Local;

/**
 * @abstract    Objects Events Manager for Spash Connectors
 */
trait ObjectsEventsTrait
{
    /** @var Local */
    private $local;
    
    /**
     * @abstract    Propagate Commit to Spalsh Server Using Connector Webservice Infos
     *
     * @param       ObjectsCommitEvent $event
     *
     * @var \Splash\Local\Local $local
     *
     * @return      bool
     */
    public function onCommitEvent(ObjectsCommitEvent $event)
    {
        //====================================================================//
        //  Identify Server & Configure Connector
        $this->identify($event->getServerId());
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
