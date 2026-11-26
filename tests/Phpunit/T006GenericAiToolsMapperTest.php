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

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Bundle\Tests\AiTools\SamplePingCapability;
use Splash\Bundle\Tests\Connectors\AiTools\MappedDisabled;
use Splash\Bundle\Tests\Connectors\AiTools\MappedEcho;
use Splash\Bundle\Tests\Connectors\DuplicateAiToolsConnector;
use Splash\Bundle\Tests\Connectors\InvalidAiToolsConnector;
use Splash\Bundle\Tests\Connectors\MappedAiToolsConnector;
use Splash\Core\Dictionary\AiTools\SplAiToolErrors;

/**
 * Test AI Tools of Classic Connectors: served by the Generic Mapper
 */
class T006GenericAiToolsMapperTest extends TestCase
{
    /**
     * Server id of the Demo Connector
     */
    private const SERVER_ID = "ThisIsMappedWsId";

    /**
     * Test Tools listed by the Mapper: types given by the Tools, disabled ones filtered out
     *
     * @throws Exception
     */
    public function testAvailableAiTools(): void
    {
        $connector = $this->getConnector();
        $available = $connector->getAvailableAiTools();
        sort($available);
        Assert::assertEquals(array(MappedEcho::TYPE, SamplePingCapability::getType()), $available);
        Assert::assertNotContains(MappedDisabled::TYPE, $available);
        Assert::assertNull($connector->getAiToolDescription(MappedDisabled::TYPE));
    }

    /**
     * Test Tools Definitions: specific Tool and Capability Tool
     *
     * @throws Exception
     */
    public function testAiToolDescription(): void
    {
        $connector = $this->getConnector();
        //====================================================================//
        // Unknown Tool
        Assert::assertNull($connector->getAiToolDescription("unknown_tool"));
        //====================================================================//
        // Specific Tool
        $echo = $connector->getAiToolDescription(MappedEcho::TYPE);
        Assert::assertNotNull($echo);
        Assert::assertEquals(MappedEcho::TYPE, $echo->getType());
        Assert::assertFalse($echo->isCapability());
        Assert::assertEquals(array("reference"), $echo->getInputsIds());
        //====================================================================//
        // Capability Tool: exposed under the type of its Capability
        $ping = $connector->getAiToolDescription(SamplePingCapability::getType());
        Assert::assertNotNull($ping);
        Assert::assertTrue($ping->isCapability());
        Assert::assertEquals(SamplePingCapability::getCode(), $ping->getCapability());
    }

    /**
     * Test Tools Execution: Standalone Tools configured, Core Tools receive the Connector
     *
     * @throws Exception
     */
    public function testAiToolExecution(): void
    {
        $connector = $this->getConnector();
        //====================================================================//
        // Standalone Tool: configured for this server
        $echo = $connector->executeAiTool(MappedEcho::TYPE, array("reference" => "ABC"));
        Assert::assertTrue($echo->isSuccess());
        Assert::assertEquals(array(array("reference" => "ABC", "server" => self::SERVER_ID)), $echo->getData());
        //====================================================================//
        // Core Tool: built with the Connector
        $ping = $connector->executeAiTool(SamplePingCapability::getType());
        Assert::assertTrue($ping->isSuccess());
        Assert::assertEquals(array(array("answer" => "pong from ".self::SERVER_ID)), $ping->getData());
        //====================================================================//
        // Same Connector, another server: Tools follow the configuration
        $connector->configure("mapped", "AnotherWsId", array());
        $other = $connector->executeAiTool(MappedEcho::TYPE, array("reference" => "ABC"));
        Assert::assertEquals("AnotherWsId", $other->getData()[0]["server"] ?? null);
        //====================================================================//
        // Missing required input
        $invalid = $connector->executeAiTool(MappedEcho::TYPE, array());
        Assert::assertFalse($invalid->isSuccess());
        Assert::assertEquals(SplAiToolErrors::INVALID_INPUTS, $invalid->getErrorCode());
    }

    /**
     * Test Unknown and Disabled Tools: a failure response, never an exception
     *
     * @throws Exception
     */
    public function testUnknownAiTool(): void
    {
        $connector = $this->getConnector();
        foreach (array("unknown_tool", MappedDisabled::TYPE) as $toolType) {
            $response = $connector->executeAiTool($toolType, array("reference" => "ABC"));
            Assert::assertFalse($response->isSuccess());
            Assert::assertEquals(SplAiToolErrors::TOOL_NOT_FOUND, $response->getErrorCode());
        }
    }

    /**
     * Test Failed Self-test: no Tools listed nor described
     *
     * @throws Exception
     */
    public function testFailedSelfTest(): void
    {
        $connector = $this->getConnector()->setSelfTestResult(false);
        Assert::assertEmpty($connector->getAvailableAiTools());
        Assert::assertNull($connector->getAiToolDescription(MappedEcho::TYPE));
    }

    /**
     * Test Two Tools of the same type are refused
     *
     * @throws Exception
     */
    public function testDuplicateTypeThrows(): void
    {
        $connector = new DuplicateAiToolsConnector();
        $connector->configure("mapped", self::SERVER_ID, array());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(SamplePingCapability::getType());
        $connector->getAvailableAiTools();
    }

    /**
     * Test a mapped class that is not a Tool is refused
     *
     * @throws Exception
     */
    public function testInvalidClassThrows(): void
    {
        $connector = new InvalidAiToolsConnector();
        $connector->configure("mapped", self::SERVER_ID, array());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("MUST implements");
        $connector->getAvailableAiTools();
    }

    /**
     * Get the Demo Connector, configured
     */
    private function getConnector(): MappedAiToolsConnector
    {
        $connector = new MappedAiToolsConnector();
        $connector->configure("mapped", self::SERVER_ID, array());

        return $connector;
    }
}
