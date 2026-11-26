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

namespace Splash\Bundle\Models\Local;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Interfaces\AiTools\AiToolInterface;
use Splash\Local\AiTools\Manager;

/**
 * Splash Bundle Local Class Tools Functions
 */
trait AiToolsTrait
{
    /**
     * Get List of Available Tools Types of the current Connector
     *
     * @return string[]
     */
    public function aiTools(): array
    {
        try {
            return $this->getConnector()->getAvailableAiTools();
        } catch (Exception $ex) {
            Splash::log()->report($ex);

            return array();
        }
    }

    /**
     * Get a Tool of the current Connector
     *
     * @throws Exception
     */
    public function aiTool(string $toolType): AiToolInterface
    {
        return new Manager($this->getConnector(), $toolType);
    }
}
