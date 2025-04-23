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

namespace Splash\Bundle\Tests\Phpunit;

use Splash\Bundle\Phpunit\Assertions\ConnectorValidator;
use Splash\Bundle\Phpunit\ConnectorTestCase;
use Splash\Validator\Phpunit\TestSequences;

/**
 * Test of Basic Fake Connector Actions
 */
class T003ConnectorActionsTest extends ConnectorTestCase
{
    /**
     * Test Fake Server Master Action
     *
     * @dataProvider serverIdProvider
     */
    public function testMasterAction(string $sequence, string $serverId): void
    {
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($sequence);
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Master Action -> OK
        ConnectorValidator::assertPublicActionWorks($connector, null, array(), "GET");
        ConnectorValidator::assertPublicActionWorks($connector, null, array(), "PUT");
        ConnectorValidator::assertPublicActionWorks($connector, null, array(), "POST");
    }

    /**
     * Test Fake Server Validate Action
     *
     * @dataProvider serverIdProvider
     */
    public function testValidateAction(string $sequence, string $serverId): void
    {
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($sequence);
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Validate Action -> OK
        ConnectorValidator::assertPublicActionWorks($connector, "validate", array(), "GET");
    }

    /**
     * Test Fake Server Fail Action
     *
     * @dataProvider serverIdProvider
     */
    public function testFailAction(string $sequence, string $serverId): void
    {
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($sequence);
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Fail Action -> GET -> OK
        ConnectorValidator::assertPublicActionFail($connector, "fail", array(), "GET");
        ConnectorValidator::assertPublicActionFail($connector, "fail", array(), "POST");
    }
}
