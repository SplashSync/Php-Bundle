<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Splash\Bundle\Models\AbstractEventSubscriber as BaseAbstractEventSubscriber;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Doctrine Events Subscriber to Listen & Commit Objects Changes
 */
abstract class AbstractEventSubscriber extends BaseAbstractEventSubscriber implements EventSubscriber
{
    /**
     * @inheritdoc
     */
    protected static $subscribedEvents = array(
        Events::postPersist => "postPersist",
        Events::postUpdate => "postUpdate",
        Events::preRemove => "preRemove",
    );

    //====================================================================//
    //  Subscriber
    //====================================================================//

    /**
     * Return the subscribed events, their methods and priorities
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return static::$subscribedEvents;
    }

    //====================================================================//
    //  Events Actions
    //====================================================================//

    /**
     * On Entity Created Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::postPersist, new GenericEvent($eventArgs->getObject()), SPL_A_CREATE);
    }

    /**
     * On Entity Updated Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::postUpdate, new GenericEvent($eventArgs->getObject()), SPL_A_UPDATE);
    }

    /**
     * On Entity Before Deleted Doctrine Event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $this->doEventAction(Events::preRemove, new GenericEvent($eventArgs->getObject()), SPL_A_DELETE);
    }
}
