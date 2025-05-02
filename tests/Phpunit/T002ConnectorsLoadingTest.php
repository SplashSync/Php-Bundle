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

use PHPUnit\Framework\Assert;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Phpunit\ConnectorTestCase;
use Splash\Core\Client\Splash;
use Splash\Validator\Phpunit\TestSequences;

/**
 * Test Availability & Loading of All Connectors
 */
class T002ConnectorsLoadingTest extends ConnectorTestCase
{
    /**
     * Test Servers are Defined
     */
    public function testServersDefinition(): void
    {
        $manager = $this->getConnectorsManager();
        Assert::assertNotEmpty($serverNames = $manager->getServersNames());
        Assert::assertArrayHasKey("node1", $serverNames);
        Assert::assertArrayHasKey("node2", $serverNames);
        Assert::assertArrayHasKey("node3", $serverNames);
    }

    /**
     * Test Loading of All Registered Connectors
     */
    public function testConnectorsLoading(): void
    {
        $manager = $this->getConnectorsManager();
        foreach (array_keys($manager->getServersNames()) as $serverId) {
            $this->assertInstanceOf(AbstractConnector::class, $manager->get($serverId));
        }
    }

    /**
     * Test Loading from Sequences
     *
     * @dataProvider serverIdProvider
     */
    public function testConnectorsLoadingFromProvider(string $sequence, string $serverId): void
    {
        Assert::assertNotEmpty($sequence);
        Assert::assertNotEmpty($serverId);
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($sequence);
        //====================================================================//
        // Verify Configured Connector
        $connector = $this->getConnector($serverId);
        Assert::assertEquals($connector->getWebserviceId(), Splash::configuration()["WsIdentifier"] ?? null);
        Assert::assertEquals($sequence, Splash::configuration()["localname"] ?? null);
    }
}
