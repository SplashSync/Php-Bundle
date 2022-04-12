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

namespace Splash\Bundle\Models\Local;

use Exception;

/**
 * Splash Bundle Local Class Tests Functions
 */
trait TestTrait
{
    //====================================================================//
    // *******************************************************************//
    //  OPTIONAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function testSequences(string $name = null): array
    {
        //====================================================================//
        // Load Configured Servers List
        $serversList = $this->getServersNames();
        //====================================================================//
        // Generate Sequence List
        if ('List' == $name) {
            return $serversList;
        }
        //====================================================================//
        // Identify Server by Name
        if (!in_array($name, $serversList, true)) {
            throw new Exception(sprintf('Server "%s" not found', $name));
        }
        //====================================================================//
        // Identify Server by Name
        $serverId = (string) array_search($name, $serversList, true);
        $webserviceId = $this->getManager()->getWebserviceId($serverId);
        $identified = $this->getManager()->identify((string) $webserviceId);
        //====================================================================//
        // Verify Server was Identify
        if ($identified !== $serverId) {
            throw new Exception(sprintf('Server Id "%s" not found in Configurations', $serverId));
        }
        //====================================================================//
        // Verify Connector is Valid
        $this->getConnector();

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function testParameters(): array
    {
        //====================================================================//
        // Init Parameters Array
        return $this->getManager()->getTestConfigurations();
    }
}
