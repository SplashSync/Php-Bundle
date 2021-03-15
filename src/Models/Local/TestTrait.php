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

use Exception;

/**
 * @abstract    Splash Bundle Local Class Tests Functions
 */
trait TestTrait
{
    //====================================================================//
    // *******************************************************************//
    //  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function testSequences($name = null)
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
        $serverdId = (string) array_search($name, $serversList, true);
        $webserviceId = $this->getManager()->getWebserviceId($serverdId);
        $indentified = $this->getManager()->identify((string) $webserviceId);
        //====================================================================//
        // Verify Server was Identify
        if ($indentified !== $serverdId) {
            throw new Exception(sprintf('Server Id "%s" not found in Configurations', $serverdId));
        }
        //====================================================================//
        // Verify Connector is Valid
        $this->getConnector();

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function testParameters()
    {
        //====================================================================//
        // Init Parameters Array
        return $this->getManager()->getTestConfigurations();
    }
}
