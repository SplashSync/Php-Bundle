<?php

declare(strict_types=1);

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

namespace Splash\Bundle\Models\Connectors;

use Splash\Core\Dictionary\AiTools\SplAiToolErrors;
use Splash\Core\Models\AiTools\AiToolDescription;
use Splash\Core\Models\AiTools\AiToolResponse;

/**
 * Default Tools implementation for Connectors: no Tools
 */
trait AiToolsTrait
{
    /**
     * Get List of Available Tools Types
     *
     * @return string[]
     */
    public function getAvailableAiTools(): array
    {
        return array();
    }

    /**
     * Get the Definition of a Tool, null when the tool does not exist
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function getAiToolDescription(string $toolType): ?AiToolDescription
    {
        return null;
    }

    /**
     * Execute a Tool: a response is always returned, success or not
     *
     * @param array<string, mixed> $inputs
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function executeAiTool(string $toolType, array $inputs = array()): AiToolResponse
    {
        return AiToolResponse::failure(
            SplAiToolErrors::TOOL_NOT_FOUND,
            sprintf('Tool "%s" does not exist on this server', $toolType)
        );
    }
}
