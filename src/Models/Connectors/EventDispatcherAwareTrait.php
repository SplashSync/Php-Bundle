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

namespace Splash\Bundle\Models\Connectors;

use ArrayObject;
use Splash\Bundle\Events\ObjectsCommitEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * @abstract    Manage Sf Event Dispatcher for Connectors
 */
trait EventDispatcherAwareTrait
{

    /**
     * @var     EventDispatcherInterface
     */
    private $EventDispatcher;
    
    /**
     * @abstract    Set Event Dispatcher
     * @param   EventDispatcherInterface    $EventDispatcher
     * @return  $this
     */
    protected function setEventDispatcher(EventDispatcherInterface $EventDispatcher)
    {
        $this->EventDispatcher   =   $EventDispatcher;
        return $this;
    }

    /**
     * @abstract    Get Event Dispatcher
     * @return  array
     */
    protected function getEventDispatcher()
    {
        return $this->EventDispatcher;
    }
    
    /**
     * @abstract    Commit an Object Change to Splash Server
     *
     * @param string                    $ObjectType
     * @param string|ArrayObject|Array  $ObjectsIds
     * @param string                    $Action
     * @param string                    $UserName
     * @param string                    $Comment
     *
     * @return void
     */
    public function commit(
        string  $ObjectType,
        $ObjectsIds,
        string  $Action,
        string  $UserName = "Unknown User",
        string  $Comment = ""
    ) {
        //==============================================================================
        //      Create Event Object
        $Event  =   new ObjectsCommitEvent(
            $this->getWebserviceId(),
            $ObjectType,
            $ObjectsIds,
            $Action,
            $UserName,
            $Comment
        );
        //==============================================================================
        //      Dispatch Event
        $this->getEventDispatcher()->dispatch(ObjectsCommitEvent::NAME, $Event);
    }
}
