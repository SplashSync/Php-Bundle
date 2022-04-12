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

namespace Splash\Bundle\Models;

use ArrayObject;
use Psr\Log\LoggerInterface;
use Splash\Bundle\Events\IdentifyServerEvent;
use Splash\Bundle\Events\ObjectFileEvent;
use Splash\Bundle\Events\ObjectsCommitEvent;
use Splash\Bundle\Events\ObjectsIdChangedEvent;
use Splash\Bundle\Events\UpdateConfigurationEvent;
use Splash\Bundle\Interfaces\ConnectorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base Class for All Splash Bundle Connectors
 */
abstract class AbstractConnector implements ConnectorInterface
{
    use Connectors\ConfigurationAwareTrait;
    use Connectors\EventDispatcherAwareTrait;
    use Connectors\LoggerAwareTrait;
    use Connectors\TrackingTrait;

    /**
     * Class Constructor
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
     *
     * @return void
     */
    public function updateConfiguration(): void
    {
        $this->getEventDispatcher()->dispatch(
            new UpdateConfigurationEvent($this->getWebserviceId(), $this->getConfiguration())
        );
    }

    /**
     * Ask for Identification of Server in Memory.
     *
     * @param string $webserviceId
     *
     * @return null|bool
     */
    public function identify(string $webserviceId): ?bool
    {
        //==============================================================================
        // Use Sf Event to Identify Server
        /** @var IdentifyServerEvent $event */
        $event = $this->getEventDispatcher()->dispatch(
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
     * Commit an Object Change to Splash Server
     *
     * @param string                   $objectType
     * @param array|ArrayObject|string $objectsIds
     * @param string                   $action
     * @param string                   $userName
     * @param string                   $comment
     *
     * @return void
     */
    public function commit(
        string  $objectType,
        $objectsIds,
        string  $action,
        string  $userName = 'Unknown User',
        string  $comment = ''
    ): void {
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
        $this->getEventDispatcher()->dispatch($event);
    }

    /**
     * Get an Object File from Splash Server
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
        $response = $this->getEventDispatcher()->dispatch($event);

        return $response->getContents();
    }

    /**
     * Tell Splash Object manager that Object ID Changed on Remote Server
     *
     * @param string $objectType  Object Type Name
     * @param string $oldObjectId Old ID for This Object
     * @param string $newObjectId New ID for This Object
     *
     * @return bool
     */
    public function objectIdChanged(string $objectType, string $oldObjectId, string $newObjectId): bool
    {
        //==============================================================================
        // Create Event Object
        $event = new ObjectsIdChangedEvent($this->getWebserviceId(), $objectType, $oldObjectId, $newObjectId);
        //==============================================================================
        //      Dispatch Event
        $this->getEventDispatcher()->dispatch($event);

        return true;
    }
}
