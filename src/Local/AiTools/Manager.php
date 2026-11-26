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

namespace Splash\Local\AiTools;

use Exception;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Core\Interfaces\AiTools\AiToolInterface;
use Splash\Core\Models\AiTools\AiToolDescription;
use Splash\Core\Models\AiTools\AiToolResponse;

/**
 * Splash Bundle Connectors Tools Access
 */
class Manager implements AiToolInterface
{
    /**
     * Current Splash Connector
     */
    private AbstractConnector $connector;

    /**
     * Current Tool Type
     */
    private string $toolType;

    /**
     * Local Tool Manager Constructor
     */
    public function __construct(AbstractConnector $connector, string $toolType)
    {
        $this->connector = $connector;
        $this->toolType = $toolType;
    }

    /**
     * @inheritDoc
     */
    public static function isDisabled(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->toolType;
    }

    /**
     * @inheritDoc
     */
    public function description(): AiToolDescription
    {
        $description = $this->connector->getAiToolDescription($this->toolType);
        if (!$description) {
            throw new Exception(sprintf('Tool "%s" does not exist on this server', $this->toolType));
        }

        return $description;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $inputs = array()): AiToolResponse
    {
        return $this->connector->executeAiTool($this->toolType, $inputs);
    }
}
