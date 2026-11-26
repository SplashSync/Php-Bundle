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

use Splash\Core\Models\AbstractAiTool;

/**
 * Disabled Tool: mapped, but never exposed
 */
class MappedDisabled extends AbstractAiTool
{
    /**
     * Tool Type
     */
    public const TYPE = "mapped_disabled";

    /**
     * @inheritDoc
     */
    protected static bool $disabled = true;

    /**
     * @inheritDoc
     */
    protected static string $toolType = self::TYPE;

    /**
     * @inheritDoc
     */
    protected static string $name = "Mapped disabled";

    /**
     * @inheritDoc
     */
    protected static string $description = "Never exposed.";

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    protected function run(array $inputs): array
    {
        return array();
    }
}
