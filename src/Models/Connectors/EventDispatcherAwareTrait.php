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

namespace Splash\Bundle\Models\Connectors;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @abstract    Manage Sf Event Dispatcher for Connectors
 */
trait EventDispatcherAwareTrait
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @abstract    Set Event Dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    protected function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @abstract    Get Event Dispatcher
     *
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
