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

namespace Splash\Bundle\Interfaces\Connectors;

use Splash\Core\Models\AiTools\AiToolDescription;
use Splash\Core\Models\AiTools\AiToolResponse;

/**
 * Connector exposes Tools: definitions with inputs & outputs, and an execution
 */
interface AiToolsInterface
{
    /**
     * Get List of Available Tools Types
     *
     * @return string[]
     */
    public function getAvailableAiTools(): array;

    /**
     * Get the Definition of a Tool, null when the tool does not exist
     */
    public function getAiToolDescription(string $toolType): ?AiToolDescription;

    /**
     * Execute a Tool: a response is always returned, success or not
     *
     * @param array<string, mixed> $inputs
     */
    public function executeAiTool(string $toolType, array $inputs = array()): AiToolResponse;
}
