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

namespace Splash\Bundle\Helpers;

/**
 * Helper for handling connector names with optional version prefix
 *
 * Bridge connectors use the format "version@name" (e.g. "2.0@shopify", "dev@faker").
 * Native connectors use plain names (e.g. "standalone", "faker").
 * This helper normalizes and compares both formats.
 */
class ConnectorNamesHelper
{
    /**
     * Extract the root connector name, stripping the version prefix if present
     *
     * "2.0@shopify" => "shopify"
     * "dev@faker"   => "faker"
     * "standalone"  => "standalone"
     */
    public static function getRootName(string $connectorName): string
    {
        $pos = strpos($connectorName, '@');

        return false !== $pos ? substr($connectorName, $pos + 1) : $connectorName;
    }

    /**
     * Check if two connector names refer to the same connector (ignoring version prefix)
     *
     * same("2.0@shopify", "shopify")       => true
     * same("dev@faker", "3.0@faker")       => true
     * same("standalone", "standalone")      => true
     * same("2.0@shopify", "faker")          => false
     * same("faker", null)                   => false
     */
    public static function same(string $srcName, mixed $targetName): bool
    {
        if (!is_string($targetName)) {
            return false;
        }

        return strtolower(self::getRootName($srcName)) === strtolower(self::getRootName($targetName));
    }
}
