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
use Splash\Core\Components\FieldsFactory;
use Splash\Core\Dictionary\SplFields;

/**
 * Sample Standalone AI Tool: echoes a reference, with the id of the server it runs on
 */
#[AsStandaloneAiTool(type: self::TYPE)]
class SampleEcho extends AbstractStandaloneAiTool
{
    /**
     * Tool Type
     */
    public const TYPE = "sample_echo";

    /**
     * @inheritDoc
     */
    protected static string $name = "Sample echo";

    /**
     * @inheritDoc
     */
    protected static string $description = "Returns the given reference, with the id of the server.";

    /**
     * @inheritDoc
     */
    protected function buildInputs(FieldsFactory $factory): void
    {
        $factory->create(SplFields::VARCHAR, "reference", "Reference")->isRequired();
    }

    /**
     * @inheritDoc
     */
    protected function buildOutputs(FieldsFactory $factory): void
    {
        $factory->create(SplFields::VARCHAR, "reference", "Reference")->isReadOnly();
        $factory->create(SplFields::VARCHAR, "server", "Server id")->isReadOnly();
    }

    /**
     * @inheritDoc
     */
    protected function run(array $inputs): array
    {
        return array(array(
            "reference" => $inputs["reference"],
            "server" => $this->getWebserviceId(),
            "undeclared" => "must never leave the server",
        ));
    }
}
