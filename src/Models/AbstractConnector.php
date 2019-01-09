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

namespace Splash\Bundle\Models;

use ArrayObject;
use Psr\Log\LoggerInterface;
use Splash\Bundle\Events\IdentifyServerEvent;
use Splash\Bundle\Events\ObjectFileEvent;
use Splash\Bundle\Events\ObjectsCommitEvent;
use Splash\Bundle\Events\UpdateConfigurationEvent;
use Splash\Bundle\Interfaces\ConnectorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @abstract Base Class for All Splash Bundle Connectors
 */
abstract class AbstractConnector implements ConnectorInterface
{
    use Connectors\ConfigurationAwareTrait;
    use Connectors\EventDispatcherAwareTrait;
    use Connectors\LoggerAwareTrait;

    /**
     * @abstract    Class Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->setEventDispatcher($eventDispatcher);
        $this->setLogger($logger);
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
     * Ask for Identifcation of Server in Memory.
     *
     * @param string $webserviceId
     *
     * @return null|bool
     */
    public function identify(string $webserviceId)
    {
        //==============================================================================
        // Use Sf Event to Identify Server
        /** @var IdentifyServerEvent $event */
        $event = $this->getEventDispatcher()->dispatch(
            IdentifyServerEvent::NAME,
            new IdentifyServerEvent($this, $webserviceId)
        );
        //==============================================================================
        // If Connection Was Rejected
        if ($event->isRejected()) {
            return null;
        }
        //==============================================================================
        // Ensure Identify Server was Ok
        return $event->isIdentified();
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

    /**
     * @abstract    Get an Object File from Splash Server
     *
     * @param string $path
     * @param string $md5
     *
     * @return array|false
     */
    public function file(string $path, string $md5)
    {
        //==============================================================================
        //      Create Event Object
        $event = new ObjectFileEvent($this->getWebserviceId(), $path, $md5);
        //==============================================================================
        //      Dispatch Event
        /** @var ObjectFileEvent $response */
        $response = $this->getEventDispatcher()->dispatch(ObjectFileEvent::NAME, $event);

        return $response->getContents();
    }
}
