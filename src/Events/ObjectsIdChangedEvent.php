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

namespace Splash\Bundle\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Connectors Object Id Changed Event
 *
 * This Event is Triggered by Any Connector to Submit Object Id was Changed.
 */
class ObjectsIdChangedEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'splash.connectors.rename';

    /**
     * @abstract    WebService Id Of Impacted Server
     *
     * @var string
     */
    private $webserviceId;

    /**
     * @abstract    Object Splash Type Name
     *
     * @var string
     */
    private $objectType;

    /**
     * @abstract    Old Objects Identifier (Id)
     *
     * @var string
     */
    private $oldObjectId;

    /**
     * @abstract    New Object Identifier (Id)
     *
     * @var string
     */
    private $newObjectId;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
     *
     * @param string $webserviceId
     * @param string $objectType
     * @param string $oldObjectId
     * @param string $newObjectId
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
     * @abstract    Get Server Id
     *
     * @return string
     */
    public function getServerId(): string
    {
        return $this->webserviceId;
    }

    /**
     * @abstract    Get Object Type Name
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @abstract    Get Old Object Id
     *
     * @return string
     */
    public function getOldObjectId(): string
    {
        return $this->oldObjectId;
    }

    /**
     * @abstract    Get New Object Id
     *
     * @return string
     */
    public function getNewObjectId(): string
    {
        return $this->newObjectId;
    }
}
