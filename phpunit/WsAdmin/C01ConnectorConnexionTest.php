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

namespace Splash\Tests\WsAdmin;

use PHPUnit\Framework\Assert;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Core\Client\Splash;
use Splash\Validator\Phpunit\TestSequences;
use Splash\Validator\SplashTestCase;

/**
 * Admin Test Suite - Connector Connexion Verifications
 */
class C01ConnectorConnexionTest extends SplashTestCase
{
    /**
     * Test Connector is Selected by setUp Events
     *
     * @dataProvider sequencesProvider
     */
    public function testConnectorIsFound(string $testSequence): void
    {
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($testSequence);
        //====================================================================//
        // Ensure Connector is Selected
        $this->assertConnectorSelected();
    }

    /**
     * Test of Final Connector Real Ping via Connector Manager
     * This is mandatory because validator generic tests are executed on SOAP Client
     * or Skipped on CiCD
     *
     * @dataProvider sequencesProvider
     */
    public function testConnectorPing(string $testSequence): void
    {
        $msg = "Test of Connector Ping Fail. Is connector correctly configured?";
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($testSequence);
        //====================================================================//
        // Ensure Connector is Selected
        $connector = $this->assertConnectorSelected();

        //====================================================================//
        // Execute Ping From Connector
        $pingResult = $connector->ping();

        //====================================================================//
        // Check Test Mode Allow Real Server Ping
        if (Splash::isCiCdMode() && !$pingResult) {
            //====================================================================//
            // Just Mark test as Incomplete
            $this->markTestSkipped($msg);
        }

        //====================================================================//
        // Ping From Connector Must Work
        $this->assertTrue($pingResult, $msg);
        //====================================================================//
        // Clean Connector Logs
        Splash::log()->cleanLog();
    }

    /**
     * Test of Final Connector Real Connect via Connector Manager
     * This is mandatory because validator generic tests are executed on SOAP Client
     * or Skipped on CiCD
     *
     * @dataProvider sequencesProvider
     */
    public function testConnectorConnect(string $testSequence): void
    {
        $msg = "Test of Connector Connect Fail. Is connector correctly configured?";
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($testSequence);
        //====================================================================//
        // Ensure Connector is Selected
        $connector = $this->assertConnectorSelected();

        //====================================================================//
        // Execute Ping From Connector
        $pingResult = $connector->ping();

        //====================================================================//
        // Check Test Mode Allow Real Server Ping
        if (Splash::isCiCdMode() && !$pingResult) {
            //====================================================================//
            // Just Mark test as Incomplete
            $this->markTestSkipped($msg);
        }

        //====================================================================//
        // Ping From Connector Must Work
        $this->assertTrue($pingResult, $msg);
        //====================================================================//
        // Clean Connector Logs
        Splash::log()->cleanLog();
    }

    /**
     * Validates that the local class implements Connectors Manager Aware
     * and ensures that a connector is properly selected.
     *
     * @return AbstractConnector Returns the selected connector instance.
     */
    private function assertConnectorSelected(): AbstractConnector
    {
        //====================================================================//
        // Check if Local Class is Connectors Manager Aware
        Assert::assertTrue(method_exists(
            $local = Splash::local(),
            "getConnector"
        ));
        //====================================================================//
        // Ensure Connector is Selected
        Assert::assertInstanceOf(
            AbstractConnector::class,
            $connector = $local->getConnector()
        );

        return $connector;
    }
}
