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
use Splash\Bundle\Connectors\Standalone;
use Splash\Bundle\Phpunit\ConnectorTestCase;
use Splash\Bundle\Tests\AiTools\SampleEcho;
use Splash\Bundle\Tests\AiTools\SamplePingCapability;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\AiTools\SplAiToolErrors;
use Splash\Core\Dictionary\AiTools\SplAiToolKinds;
use Splash\Validator\Phpunit\TestSequences;

/**
 * Test AI Tools of Connectors: tagged services on Standalone, none by default
 */
class T005ConnectorAiToolsTest extends ConnectorTestCase
{
    /**
     * Test Tools exposed by each Connector
     *
     * @dataProvider serverIdProvider
     *
     * @throws Exception
     */
    public function testAvailableAiTools(string $sequence, string $serverId): void
    {
        TestSequences::configure($sequence);
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Standalone Connector serves tagged services, others have no tools
        $expected = ($connector instanceof Standalone)
            ? array(SampleEcho::TYPE, SamplePingCapability::getType())
            : array();
        $available = $connector->getAvailableAiTools();
        sort($available);
        Assert::assertEquals($expected, $available);
        //====================================================================//
        // Local Class gives the same list to Splash Core
        $local = Splash::aiTools();
        sort($local);
        Assert::assertEquals($expected, $local);
    }

    /**
     * Test Tool Definition
     *
     * @dataProvider serverIdProvider
     *
     * @throws Exception
     */
    public function testAiToolDescription(string $sequence, string $serverId): void
    {
        TestSequences::configure($sequence);
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Unknown Tool
        Assert::assertNull($connector->getAiToolDescription("unknown_tool"));
        if (!$connector instanceof Standalone) {
            Assert::assertNull($connector->getAiToolDescription(SampleEcho::TYPE));

            return;
        }
        //====================================================================//
        // Known Tool, from the Connector
        $description = $connector->getAiToolDescription(SampleEcho::TYPE);
        Assert::assertNotNull($description);
        Assert::assertEquals(SampleEcho::TYPE, $description->getType());
        Assert::assertEquals(SplAiToolKinds::READ, $description->getKind());
        Assert::assertFalse($description->isCapability());
        Assert::assertEquals(array("reference"), $description->getInputsIds());
        Assert::assertEquals(array("reference", "server"), array_column($description->getOutputs(), "id"));
        //====================================================================//
        // Known Tool, from Splash Core
        Assert::assertEquals(
            $description->toArray(),
            Splash::aiTool(SampleEcho::TYPE)->description()->toArray()
        );
    }

    /**
     * Test a Capability Tool is exposed under the type of its Capability, not under its tag
     *
     * @dataProvider serverIdProvider
     *
     * @throws Exception
     */
    public function testCapabilityGivesTheType(string $sequence, string $serverId): void
    {
        TestSequences::configure($sequence);
        $connector = $this->getConnector($serverId);
        if (!$connector instanceof Standalone) {
            Assert::assertEmpty($connector->getAvailableAiTools());

            return;
        }
        Assert::assertEquals("sample_ping", SamplePingCapability::getType());
        //====================================================================//
        // The type given to the tag is ignored
        Assert::assertNull($connector->getAiToolDescription("my_own_ping_name"));
        $description = $connector->getAiToolDescription("sample_ping");
        Assert::assertNotNull($description);
        Assert::assertEquals("sample_ping", $description->getType());
        Assert::assertTrue($description->isCapability());
        Assert::assertEquals(SamplePingCapability::getCode(), $description->getCapability());
        //====================================================================//
        // Execution
        $response = $connector->executeAiTool("sample_ping");
        Assert::assertTrue($response->isSuccess());
        Assert::assertEquals(array(array("answer" => "pong")), $response->getData());
        Assert::assertEquals("sample_ping", $response->getMeta("tool"));
    }

    /**
     * Test Tool Execution
     *
     * @dataProvider serverIdProvider
     *
     * @throws Exception
     */
    public function testAiToolExecution(string $sequence, string $serverId): void
    {
        TestSequences::configure($sequence);
        $connector = $this->getConnector($serverId);
        //====================================================================//
        // Unknown Tool: a response, with its error
        $unknown = $connector->executeAiTool("unknown_tool", array("reference" => "ABC"));
        Assert::assertFalse($unknown->isSuccess());
        Assert::assertEquals(SplAiToolErrors::TOOL_NOT_FOUND, $unknown->getErrorCode());
        if (!$connector instanceof Standalone) {
            return;
        }
        //====================================================================//
        // Success: declared outputs only, configured for this server
        $response = $connector->executeAiTool(SampleEcho::TYPE, array("reference" => "ABC"));
        Assert::assertTrue($response->isSuccess());
        Assert::assertEquals(
            array(array("reference" => "ABC", "server" => $connector->getWebserviceId())),
            $response->getData()
        );
        Assert::assertEquals(SampleEcho::TYPE, $response->getMeta("tool"));
        //====================================================================//
        // Missing required input
        $invalid = $connector->executeAiTool(SampleEcho::TYPE, array());
        Assert::assertFalse($invalid->isSuccess());
        Assert::assertEquals(SplAiToolErrors::INVALID_INPUTS, $invalid->getErrorCode());
        //====================================================================//
        // Same execution from Splash Core
        Assert::assertEquals(
            $response->getData(),
            Splash::aiTool(SampleEcho::TYPE)->execute(array("reference" => "ABC"))->getData()
        );
    }
}
