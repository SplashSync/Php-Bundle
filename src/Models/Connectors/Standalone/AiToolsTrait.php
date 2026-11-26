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

namespace Splash\Bundle\Models\Connectors\Standalone;

use Exception;
use Splash\Bundle\Dictionary\StandaloneServiceTags;
use Splash\Bundle\Models\AbstractStandaloneAiTool;
use Splash\Core\Dictionary\AiTools\SplAiToolErrors;
use Splash\Core\Interfaces\AiTools\CapabilityAwareInterface;
use Splash\Core\Models\AiTools\AiToolDescription;
use Splash\Core\Models\AiTools\AiToolResponse;

/**
 * AI Tools of the Standalone Connector: served by tagged services
 */
trait AiToolsTrait
{
    /**
     * List of Tagged Tools Services
     *
     * @var array<string, AbstractStandaloneAiTool>
     */
    private array $taggedAiTools = array();

    /**
     * {@inheritdoc}
     */
    public function getAvailableAiTools(): array
    {
        return array_keys($this->taggedAiTools);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getAiToolDescription(string $toolType): ?AiToolDescription
    {
        return isset($this->taggedAiTools[$toolType])
            ? $this->getAiToolService($toolType)->description()
            : null
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function executeAiTool(string $toolType, array $inputs = array()): AiToolResponse
    {
        if (!isset($this->taggedAiTools[$toolType])) {
            return AiToolResponse::failure(
                SplAiToolErrors::TOOL_NOT_FOUND,
                sprintf('Tool "%s" does not exist on this server', $toolType)
            );
        }

        return $this->getAiToolService($toolType)->execute($inputs);
    }

    /**
     * Register a Tagged Standalone Tool Service
     *
     * A Tool implementing a Capability is registered under the type of the Capability,
     * whatever type its tag gives. Any other Tool requires a type.
     *
     * @throws Exception
     */
    public function registerAiToolService(?string $toolType, AbstractStandaloneAiTool $toolService): void
    {
        if ($toolService instanceof CapabilityAwareInterface) {
            $toolType = $toolService->getCapability()::getType();
        }
        if (empty($toolType)) {
            throw new Exception(sprintf(
                'Standalone Tool "%s" has no type. Give one to its "%s" tag.',
                get_class($toolService),
                StandaloneServiceTags::AI_TOOL
            ));
        }
        if (isset($this->taggedAiTools[$toolType]) && ($this->taggedAiTools[$toolType] !== $toolService)) {
            throw new Exception(sprintf(
                'Standalone Tool "%s" is already served by "%s", "%s" cannot use the same type.',
                $toolType,
                get_class($this->taggedAiTools[$toolType]),
                get_class($toolService)
            ));
        }
        $this->taggedAiTools[$toolType] = $toolService;
    }

    /**
     * Get Configured Standalone Tool Service
     *
     * @throws Exception
     */
    public function getAiToolService(string $toolType): AbstractStandaloneAiTool
    {
        if (!isset($this->taggedAiTools[$toolType])) {
            throw new Exception(sprintf(
                'Standalone Tool "%s" is not defined. Did you tag your service as "%s"?',
                $toolType,
                StandaloneServiceTags::AI_TOOL
            ));
        }
        $this->taggedAiTools[$toolType]->configure($toolType, $this->getWebserviceId(), $this->getConfiguration());

        return $this->taggedAiTools[$toolType];
    }
}
