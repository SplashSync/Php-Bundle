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

namespace Splash\Bundle\Models\Local;

use Splash\Core\SplashCore as Splash;
use Splash\Models\FileProviderInterface;

/**
 * Splash Bundle Local Class Files Functions
 */
trait FilesTrait
{
    /**
     * {@inheritDoc}
     */
    public function hasFile($file = null, $md5 = null)
    {
        //====================================================================//
        // Check if Current Connector is a File Provider
        $connector = $this->getConnector();
        if ($connector instanceof FileProviderInterface) {
            return $connector->hasFile($file, $md5);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function readFile($file = null, $md5 = null)
    {
        //====================================================================//
        // Check if Current Connector is a File Provider
        $connector = $this->getConnector();
        if ($connector instanceof FileProviderInterface) {
            return $connector->readFile($file, $md5);
        }

        return false;
    }
}
