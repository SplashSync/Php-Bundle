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

namespace Splash\Bundle\Models\Connectors;

use Splash\Bundle\Interfaces\Connectors\TrackingInterface;
use Splash\Core\Dictionary\SplOperations;

/**
 * Connector Functions for Tracking Objects Changes
 */
trait TrackingTrait
{
    /**
     * Check if This Connector is Self Tracking Objects Changes
     *
     * @return bool
     */
    public function isTrackingConnector(): bool
    {
        //====================================================================//
        // Check if Connector Implements Tracking Interface
        return is_subclass_of($this, TrackingInterface::class);
    }

    /**
     * Get List of Objects Available for Self Changes Tracking
     *
     * @return array
     */
    public function getTrackedObjects(): array
    {
        $response = array();
        //====================================================================//
        // Check if Connector Implements Tracking Interface
        if (!is_subclass_of($this, TrackingInterface::class)) {
            return $response;
        }
        //==============================================================================
        // Walk on Connector Available Objects
        foreach ($this->getAvailableObjects() as $objectType) {
            if (!$this->isObjectTracked($objectType)) {
                continue;
            }
            $response[] = $objectType;
        }

        return $response;
    }

    /**
     * Get List of Updated Object Ids & Commit Changes
     *
     * @param string $objectType Object Type Name
     *
     * @return int Number of Changes Commited
     */
    public function doObjectChangesTracking(string $objectType): int
    {
        $commited = 0;
        //====================================================================//
        // Check if Connector Implements Tracking Interface
        if (!is_subclass_of($this, TrackingInterface::class)) {
            return $commited;
        }
        //==============================================================================
        // Read List of Updated Objects
        $updatedIds = $this->getObjectUpdatedIds($objectType);
        if (!empty($updatedIds)) {
            $this->commit(
                $objectType,
                $updatedIds,
                SplOperations::UPDATE,
                $this->getProfile()["name"],
                "Change Detected by Connector"
            );
            $commited += count($updatedIds);
        }
        //==============================================================================
        // Read List of Deleted Objects
        $deletedIds = $this->getObjectDeletedIds($objectType);
        if (!empty($deletedIds)) {
            $this->commit(
                $objectType,
                $deletedIds,
                SplOperations::DELETE,
                $this->getProfile()["name"],
                "Delete Detected by Connector"
            );
            $commited += count($deletedIds);
        }

        return $commited;
    }
}
