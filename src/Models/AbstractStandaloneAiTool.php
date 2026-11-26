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

namespace Splash\Bundle\Models;

use Splash\Bundle\Interfaces\Connectors\ConfigurationInterface;
use Splash\Bundle\Models\Connectors\ConfigurationAwareTrait;
use Splash\Core\Interfaces\AiTools\CapabilityAwareInterface;
use Splash\Core\Models\AbstractAiTool;

/**
 * Base Class for Standalone Connector Tools
 */
abstract class AbstractStandaloneAiTool extends AbstractAiTool implements ConfigurationInterface
{
    use ConfigurationAwareTrait;

    /**
     * @inheritDoc
     *
     * A Capability gives its own type: same name on every server.
     * Otherwise, the configured type, or the Tool's own one before configuration.
     */
    public function getType(): string
    {
        if (($this instanceof CapabilityAwareInterface) || !isset($this->type)) {
            return parent::getType();
        }

        return $this->getSplashType();
    }
}
