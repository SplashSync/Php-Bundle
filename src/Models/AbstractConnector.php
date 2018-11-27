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

namespace Splash\Bundle\Models;

use ArrayObject;
use Splash\Bundle\Events\ObjectsCommitEvent;
use Splash\Bundle\Events\UpdateConfigurationEvent;
use Splash\Bundle\Interfaces\ConnectorInterface;
use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;
use Splash\Bundle\Models\Connectors\EventDispatcherAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @abstract Base Class for All Splash Bundle Connectors
 */
abstract class AbstractConnector implements ConnectorInterface
{
    use ConfigurationAwareTrait;
    use EventDispatcherAwareTrait;

    /**
     * @abstract    Class Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->setEventDispatcher($eventDispatcher);
    }

    /**
     * Ask for Update of Server Configuration in Memory.
     */
    public function updateConfiguration()
    {
        $this->getEventDispatcher()->dispatch(
            UpdateConfigurationEvent::NAME,
            new UpdateConfigurationEvent($this->getWebserviceId(), $this->getConfiguration())
        );
    }

    /**
     * @abstract    Commit an Object Change to Splash Server
     *
     * @param string                   $objectType
     * @param array|ArrayObject|string $objectsIds
     * @param string                   $action
     * @param string                   $userName
     * @param string                   $comment
     */
    public function commit(string  $objectType, $objectsIds, string  $action, string  $userName = 'Unknown User', string  $comment = '')
    {
        //==============================================================================
        //      Create Event Object
        $event = new ObjectsCommitEvent(
            $this->getWebserviceId(),
            $objectType,
            $objectsIds,
            $action,
            $userName,
            $comment
        );
        //==============================================================================
        //      Dispatch Event
        $this->getEventDispatcher()->dispatch(ObjectsCommitEvent::NAME, $event);
    }
}
