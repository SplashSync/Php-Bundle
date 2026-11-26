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

use Splash\Core\Components\FieldsFactory;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Models\AbstractCapability;

/**
 * Sample Capability: its tools are always exposed as sample_ping
 */
class SamplePingCapability extends AbstractCapability
{
    /**
     * @inheritDoc
     */
    public static function getCode(): string
    {
        return "Splash.Capabilities.Sample.Ping";
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return "sample_ping";
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "Sample ping";
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Answers pong.";
    }

    /**
     * @inheritDoc
     */
    public function buildOutputs(FieldsFactory $factory): void
    {
        $factory->create(SplFields::VARCHAR, "answer", "Answer")->isReadOnly();
    }
}
