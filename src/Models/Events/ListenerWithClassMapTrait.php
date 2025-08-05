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

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Make Listener aware of a Splash Object Type Class Map
 */
trait ListenerWithClassMapTrait
{
    /**
     * Populate List of Splash Entities Managed by this Listener
     *
     *  - key: Class Name or Interface Name
     *  - value: Splash Object Type Name
     *
     * @return array<class-string, string>
     */
    abstract protected static function getClassMap(): array;

    /**
     * Detect Object Type from Received Event
     * Null Types will Filter Events from Beginning
     */
    protected function getObjectType(GenericEvent $event): ?string
    {
        $subject = $event->getSubject();

        return is_object($subject)
            ? self::isInClassMap(get_class($subject))
            : null
        ;
    }

    /**
     * Check if Object is Managed by Splash
     * If found, return Splash Object Type
     *
     * @param class-string $className Current Object Class
     */
    protected function isInClassMap(string $className): ?string
    {
        static $classMap = null;
        //====================================================================//
        // Ensure Class Map Init
        $classMap ??= static::getClassMap();
        //====================================================================//
        // Walk on Managed Entities
        foreach ($classMap as $entityClass => $objectType) {
            if (is_a($className, $entityClass, true)) {
                return $objectType;
            }
            if (is_subclass_of($className, $entityClass)) {
                return $objectType;
            }
        }

        return null;
    }
}
