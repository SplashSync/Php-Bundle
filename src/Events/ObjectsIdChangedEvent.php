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
 * Connectors Object ID Changed Event
 *
 * This Event is Triggered by Any Connector to Submit Object ID was Changed.
 */
class ObjectsIdChangedEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'Splash\Bundle\Events\ObjectsIdChangedEvent';

    /**
     * WebService ID Of Impacted Server
     *
     * @var string
     */
    private string $webserviceId;

    /**
     * Object Splash Type Name
     *
     * @var string
     */
    private string $objectType;

    /**
     * Old Objects Identifier (ID)
     *
     * @var string
     */
    private string $oldObjectId;

    /**
     * New Object Identifier (ID)
     *
     * @var string
     */
    private string $newObjectId;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param string $webserviceId WebService ID
     * @param string $objectType   Object Splash Type Name
     * @param string $oldObjectId  Old Objects Identifier (ID)
     * @param string $newObjectId  New Object Identifier (ID)
     */
    public function __construct(string  $webserviceId, string  $objectType, string  $oldObjectId, string  $newObjectId)
    {
        //==============================================================================
        //      Basic Data Storage
        $this->webserviceId = $webserviceId;
        $this->objectType = $objectType;
        $this->oldObjectId = $oldObjectId;
        $this->newObjectId = $newObjectId;
    }

    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================

    /**
     * Get Server Id
     *
     * @return string
     */
    public function getServerId(): string
    {
        return $this->webserviceId;
    }

    /**
     * Get Object Type Name
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * Get Old Object Id
     *
     * @return string
     */
    public function getOldObjectId(): string
    {
        return $this->oldObjectId;
    }

    /**
     * Get New Object Id
     *
     * @return string
     */
    public function getNewObjectId(): string
    {
        return $this->newObjectId;
    }
}
