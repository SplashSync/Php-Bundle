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

namespace Splash\Bundle\Tests\Phpunit\Connectors;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Tests\WebTestCase;

class C000DefaultActionsTest extends WebTestCase
{
    /**
     * Test Fake Server Master Action
     *
     * @throws Exception
     *
     * @dataProvider fakeServerProvider
     */
    public function testMasterAction(string $serverId): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        $this->assertInstanceOf(AbstractConnector::class, $connector);
        //====================================================================//
        // Master Action -> OK
        $this->assertPublicActionWorks($connector, null, array(), "GET");
        $this->assertPublicActionWorks($connector, null, array(), "PUT");
        $this->assertPublicActionWorks($connector, null, array(), "POST");
    }

    /**
     * Test Fake Server Validate Action
     *
     * @throws Exception
     *
     * @dataProvider fakeServerProvider
     */
    public function testValidateAction(string $serverId): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        $this->assertInstanceOf(AbstractConnector::class, $connector);
        //====================================================================//
        // Validate Action -> OK
        $this->assertPublicActionWorks($connector, "validate", array(), "GET");
    }

    /**
     * Test Fake Server Fail Action
     *
     * @throws Exception
     *
     * @dataProvider fakeServerProvider
     */
    public function testFailAction(string $serverId): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector($serverId);
        $this->assertInstanceOf(AbstractConnector::class, $connector);
        //====================================================================//
        // Fail Action -> GET -> OK
        $this->assertPublicActionFail($connector, "fail", array(), "GET");
        $this->assertPublicActionFail($connector, "fail", array(), "POST");
    }

    /**
     * Fake Splash Server NAmes Provider
     *
     * @return array[]
     */
    public function fakeServerProvider(): array
    {
        return array(
            "Fake 1" => array("node1"),
            "Fake 2" => array("node2"),
        );
    }
}
