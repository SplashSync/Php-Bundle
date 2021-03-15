<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Interfaces\Connectors;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Define Required structure for Connectors that Can Track Objects Changes
 */
interface TrackingInterface
{
    /**
     * Ask Connector if Given Object Type use Changes Tracking
     *
     * @param string $objectType Remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return bool
     */
    public function isObjectTracked(string $objectType): bool;

    /**
     * Ask Connector for delay Between Two Change Tracking (in Minutes)
     *
     * @param string $objectType Remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return int
     */
    public function getObjectTrackingDelay(string $objectType): int;

    /**
     * Ask Connector for Changed Objects Ids
     *
     * @param string $objectType remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function getObjectUpdatedIds(string $objectType): array;

    /**
     * Ask Connector for Deleted Objects Ids
     *
     * @param string $objectType remote Object Type Name
     *
     * @throws NotFoundHttpException
     *
     * @return array
     */
    public function getObjectDeletedIds(string $objectType): array;

    /**
     * Get List of Updated Object Ids & Commit Changes
     *
     * @param string $objectType Object Type Name
     *
     * @return int Number of Changes Commited
     */
    public function doObjectChangesTracking(string $objectType): int;
}
