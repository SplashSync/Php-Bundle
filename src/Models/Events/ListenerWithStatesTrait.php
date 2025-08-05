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

namespace Splash\Bundle\Models\Events;

/**
 * Make an Events Listener aware of Enable / Disable Events
 *
 * Allow User to Disable desired states for being filtered
 */
trait ListenerWithStatesTrait
{
    /**
     * Events Triggers States
     *
     * @var array
     */
    protected static array $disabledStates = array();

    /**
     * Enable/Disable on Given Events Names
     */
    public static function setState(string $eventName, bool $status): void
    {
        $eventName = static::class.":".$eventName;
        if ($status && isset(static::$disabledStates[$eventName])) {
            unset(static::$disabledStates[$eventName]);
        }

        if (!$status && !isset(static::$disabledStates[$eventName])) {
            static::$disabledStates[$eventName] = true;
        }
    }

    /**
     * Enable All Events when Running the Installer
     */
    public static function setAllStatesEnabled(): void
    {
        static::$disabledStates = array();
    }

    /**
     * Check if Event is Allowed for an Event name
     */
    public static function isStateEnabled(string $eventName): bool
    {
        $eventName = static::class.":".$eventName;

        return !isset(static::$disabledStates[$eventName]);
    }
}
