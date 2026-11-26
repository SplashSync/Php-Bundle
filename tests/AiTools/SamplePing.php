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

namespace Splash\Bundle\Tests\AiTools;

use Splash\Bundle\Attributes\AsStandaloneAiTool;
use Splash\Bundle\Models\AbstractStandaloneAiTool;
use Splash\Core\Interfaces\AiTools\CapabilityAwareInterface;

/**
 * Sample Capability Tool: tagged with a type of its own, exposed under the one of its Capability
 */
#[AsStandaloneAiTool(type: "my_own_ping_name")]
class SamplePing extends AbstractStandaloneAiTool implements CapabilityAwareInterface
{
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
        return array(array("answer" => "pong"));
    }
}
