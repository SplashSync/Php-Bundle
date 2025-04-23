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

use Splash\Models\Objects\PrimaryKeysAwareInterface;

/**
 * Manager Access to Generic Splash Objects Primary Features for Standard Connectors
 *
 * Connector Only Map Objects Type => Classname and Mapper will do the rest
 *
 * Connector must implement Splash\Bundle\Interfaces\Connectors\PrimaryKeysInterface
 */
trait GenericObjectPrimaryMapperTrait
{
    /**
     * Identify Object Using Primary Keys
     *
     * Splash will send a list of Fields values to Search for Objects in Database.
     *
     * If One AND Only One Object is Identified
     * this function must return its ID, else NULL
     *
     * @param string                $objectType Remote Object Type Name
     * @param array<string, string> $keys       Primary Keys List
     *
     * @return null|string
     *
     * @remark  This Feature is Optional but Highly recommended for
     *          Objects alike Products(SKU), Users (Email), and more...
     *
     * @since 2.0.0
     */
    public function getObjectIdByPrimary(string $objectType, array $keys): ?string
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return null;
        }
        //====================================================================//
        // Get Generic Object Class
        $objectClass = $this->getObjectLocalClass($objectType);
        //====================================================================//
        // Check Object Service
        if ($objectClass instanceof PrimaryKeysAwareInterface) {
            //====================================================================//
            // Forward Action
            return $objectClass->getByPrimary($keys) ?: null;
        }

        return null;
    }
}
