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

namespace Splash\Bundle\Phpunit\Providers;

use Exception;

/**
 * Connectors Datasets Provider for Splash Phpunit Test Cases
 */
trait ConnectorsProviderTrait
{
    /**
     * Data Provider: Tests Sequences + Splash Server ID
     *
     * @throws Exception
     *
     * @return array<string, array>
     */
    public function serverIdProvider(): array
    {
        $result = array();
        //====================================================================//
        // Boot Test Environment
        self::setUp();
        //====================================================================//
        // Walk on Defined Servers
        $manager = $this->getConnectorsManager();
        foreach ($manager->getServersNames() as $serverId => $serverName) {
            //====================================================================//
            // Add Server to List
            $dataSetName = '['.$serverId."] ".$serverName;
            $result[$dataSetName] = array(
                'sequence' => $serverName,
                'serverId' => $serverId
            );
        }
        //====================================================================//
        // Stop Test Environment
        self::tearDown();

        return $result;
    }
}
