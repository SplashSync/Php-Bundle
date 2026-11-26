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

use Exception;
use Splash\Bundle\Models\AbstractStandaloneAiTool;
use Splash\Core\Dictionary\AiTools\SplAiToolErrors;
use Splash\Core\Interfaces\AiTools\AiToolInterface;
use Splash\Core\Models\AiTools\AiToolDescription;
use Splash\Core\Models\AiTools\AiToolResponse;

/**
 * Access to Generic Splash AI Tools for Standard Connectors
 *
 * Connector only lists its Tools classes, the Mapper does the rest.
 * The type of each Tool is given by the Tool itself, by its Capability when it implements one:
 * a hand-written key could contradict it, so the map is a list, not a type => class map.
 *
 * Tools must implement Splash\Core\Interfaces\AiTools\AiToolInterface,
 * they receive the Connector in their constructor.
 * Tools that extend AbstractStandaloneAiTool are configured before use.
 *
 * Map is defined on STATIC variable $aiToolsMap
 */
trait GenericAiToolsMapperTrait
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getAvailableAiTools(): array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return array();
        }

        return array_keys($this->getMappedAiTools());
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getAiToolDescription(string $toolType): ?AiToolDescription
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return null;
        }
        $tool = $this->getMappedAiTools()[$toolType] ?? null;

        return $tool ? $tool->description() : null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function executeAiTool(string $toolType, array $inputs = array()): AiToolResponse
    {
        $tool = $this->getMappedAiTools()[$toolType] ?? null;
        if (!$tool) {
            return AiToolResponse::failure(
                SplAiToolErrors::TOOL_NOT_FOUND,
                sprintf('Tool "%s" does not exist on this server', $toolType)
            );
        }

        return $tool->execute($inputs);
    }

    /**
     * Build the enabled Tools of the map, by type
     *
     * Tools are built on every call: the same Connector may serve several servers,
     * each Tool must work with the current configuration.
     *
     * @throws Exception When a class is invalid, or two Tools give the same type
     *
     * @return array<string, AiToolInterface>
     */
    private function getMappedAiTools(): array
    {
        $tools = array();
        foreach (static::$aiToolsMap as $toolClass) {
            //====================================================================//
            // Safety Check => Validate Tool Class
            $error = $this->isValidAiToolClass($toolClass);
            if ($error) {
                throw new Exception(sprintf("Invalid AI Tool %s: %s", print_r($toolClass, true), $error));
            }
            /** @var class-string<AiToolInterface> $toolClass */
            if ($toolClass::isDisabled()) {
                continue;
            }
            //====================================================================//
            // Create Tool Class, the Tool gives its type
            $tool = new $toolClass($this);
            $toolType = $tool->getType();
            //====================================================================//
            // Safety Check => One Tool per type
            if (isset($tools[$toolType])) {
                throw new Exception(sprintf(
                    'AI Tool "%s" is already served by "%s", "%s" cannot use the same type.',
                    $toolType,
                    get_class($tools[$toolType]),
                    $toolClass
                ));
            }
            //====================================================================//
            // If Standalone Tool => Configure it!
            if ($tool instanceof AbstractStandaloneAiTool) {
                $tool->configure($toolType, $this->getWebserviceId(), $this->getConfiguration());
            }
            $tools[$toolType] = $tool;
        }

        return $tools;
    }

    /**
     * Validate AI Tool Class Name
     *
     * @param mixed $className
     */
    private function isValidAiToolClass($className): ?string
    {
        //====================================================================//
        // Safety Check => Tool Class is String
        if (!is_string($className)) {
            return "AI Tool Class is Not a String";
        }
        //====================================================================//
        // Safety Check => Class Exists
        if (!class_exists($className)) {
            return "AI Tool Class Not Found";
        }
        //====================================================================//
        // Safety Check => Class Implements
        if (!is_subclass_of($className, AiToolInterface::class)) {
            return "AI Tool Class MUST implements ".AiToolInterface::class;
        }

        return null;
    }
}
