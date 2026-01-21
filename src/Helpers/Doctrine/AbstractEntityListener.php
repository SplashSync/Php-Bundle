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

namespace Splash\Bundle\Helpers\Doctrine;

use Doctrine\ORM\Events;
use Splash\Bundle\Models\Events\AbstractChangesListener;
use Splash\Core\Dictionary\SplOperations;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Listen to Doctrine Entity Listeners Events & Commit Objects Changes.
 *
 * Use this class when using #[AsEntityListener(entity: MyClass::class)]
 */
abstract class AbstractEntityListener extends AbstractChangesListener
{
    //====================================================================//
    //  Events Actions
    //====================================================================//

    /**
     * On Entity Created Doctrine Event
     */
    public function postPersist(object $subject): void
    {
        $this->doEventAction(
            Events::postPersist,
            new GenericEvent($subject),
            SplOperations::CREATE
        );
    }

    /**
     * On Entity Updated Doctrine Event
     */
    public function postUpdate(object $subject): void
    {
        $this->doEventAction(
            Events::postUpdate,
            new GenericEvent($subject),
            SplOperations::UPDATE
        );
    }

    /**
     * On Entity Before Deleted Doctrine Event
     */
    public function preRemove(object $subject): void
    {
        $this->doEventAction(
            Events::preRemove,
            new GenericEvent($subject),
            SplOperations::DELETE
        );
    }
}
