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

namespace Splash\Bundle\Interfaces\Objects;

/**
 * Define Required structure for Self Change Tracking Objects
 */
interface TrackingInterface
{
    /**
     * Get delay Between Two Change Tracking (in Minutes)
     *
     * @return int
     */
    public function getTrackingDelay(): int;

    /**
     * Fetch List of Updated Objects Ids
     *
     * @return array
     */
    public function getUpdatedIds(): array;

    /**
     * Fetch List of Deleted Objects Ids
     *
     * @return array
     */
    public function getDeletedIds(): array;
}
