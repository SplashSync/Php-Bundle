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

namespace Splash\Bundle\Tests\Connectors\AiTools;

use Splash\Bundle\Tests\AiTools\SamplePingCapability;
use Splash\Bundle\Tests\Connectors\MappedAiToolsConnector;
use Splash\Core\Interfaces\AiTools\CapabilityAwareInterface;
use Splash\Core\Models\AbstractAiTool;

/**
 * Mapped Capability Tool: a Core Tool, it receives the Connector and answers with its server id
 */
class MappedPing extends AbstractAiTool implements CapabilityAwareInterface
{
    /**
     * Connector serving this Tool
     *
     * @var MappedAiToolsConnector
     */
    private MappedAiToolsConnector $connector;

    /**
     * Tools of a Mapper receive their Connector
     */
    public function __construct(MappedAiToolsConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @inheritDoc
     */
    public function getCapability(): string
    {
        return SamplePingCapability::class;
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function run(array $inputs): array
    {
        return array(array("answer" => "pong from ".$this->connector->getWebserviceId()));
    }
}
