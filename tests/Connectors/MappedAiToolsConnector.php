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

namespace Splash\Bundle\Tests\Connectors;

use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;
use Splash\Bundle\Models\Connectors\GenericAiToolsMapperTrait;

/**
 * Demo Connector: AI Tools served by the Generic Mapper
 *
 * Only what the Mapper needs from a Connector: configuration and self-test.
 */
class MappedAiToolsConnector
{
    use ConfigurationAwareTrait;
    use GenericAiToolsMapperTrait;

    /**
     * AI Tools Classes: Tool Type is given by each Tool
     *
     * @var array<int, class-string>
     */
    protected static array $aiToolsMap = array(
        AiTools\MappedEcho::class,
        AiTools\MappedPing::class,
        AiTools\MappedDisabled::class,
    );

    /**
     * Result of the Connector Self-test
     *
     * @var bool
     */
    private bool $selfTestResult = true;

    /**
     * Connector Self-test
     */
    public function selfTest(): bool
    {
        return $this->selfTestResult;
    }

    /**
     * Force the Result of the Connector Self-test
     */
    public function setSelfTestResult(bool $selfTestResult): static
    {
        $this->selfTestResult = $selfTestResult;

        return $this;
    }
}
