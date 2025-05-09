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

namespace Splash\Bundle\Phpunit\Tests\Read;

use PHPUnit\Framework\Assert;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Methods\SplObjectMethods;
use Splash\Core\Dictionary\SplServices;
use Splash\Local\Local;
use Splash\Validator\Assertions\Client\ActionValidator;
use Splash\Validator\Assertions\Objects\ObjectReadValidator;
use Splash\Validator\Phpunit\TestContext;
use Splash\Validator\Phpunit\Tests\Read\AbstractObjectReadingTest;
use Splash\Validator\Services\ObjectDataComparator;

/**
 * Base Class for Executing Reading of Multiple Objects at Once Tests
 */
abstract class AbstractObjectMultiReadingTest extends AbstractObjectReadingTest
{
    /**
     * Storage for Existing Objects IDs
     *
     * @var string[]
     */
    private array $objectIds = array();

    /**
     * Storage for Existing Objects Datasets
     *
     * @var array[]
     */
    private array $objectsDatasets = array();

    /**
     * @param string[] $objectIds
     * @param array[]  $objectDatasets
     */
    public function __construct(array $objectIds, array $objectDatasets)
    {
        $this->objectIds = $objectIds;
        $this->objectsDatasets = $objectDatasets;
    }

    /**
     * @inheritDoc
     */
    public function executeFromModule(string $objectType, array $dataset, bool $verify = true): void
    {
        //====================================================================//
        // Safety Check => Verify Dataset
        ObjectReadValidator::assertValidDataset($dataset);
        Assert::assertNotEmpty($this->objectIds);
        Assert::assertNotEmpty($this->objectsDatasets);
        Assert::assertCount(count($this->objectIds), $this->objectsDatasets);
        //====================================================================//
        // Execute Action Directly on Connector
        /** @var Local $local */
        $local = Splash::local();
        $responses = $local->getConnector()->getObject($objectType, $this->objectIds, $dataset);
        //====================================================================//
        // Verify Response
        $this->verifyResponses($objectType, $responses, $verify);
    }

    /**
     * @inheritDoc
     */
    public function executeFromService(string $objectType, array $dataset, bool $verify = true): void
    {
        //====================================================================//
        // Safety Check => Verify Dataset
        ObjectReadValidator::assertValidDataset($dataset);
        Assert::assertNotEmpty($this->objectIds);
        Assert::assertNotEmpty($this->objectsDatasets);
        Assert::assertCount(count($this->objectIds), $this->objectsDatasets);

        //====================================================================//
        //   Build Multiple Read Tasks List
        $tasks = array();
        foreach ($this->objectIds as $index => $objectId) {
            $tasks[$index] = array(
                "type" => $objectType,
                "id" => $objectId,
                "fields" => $dataset
            );
        }

        //====================================================================//
        // Execute Action From Splash Server to Module
        $responses = ActionValidator::multiple(
            SplServices::OBJECTS,
            SplObjectMethods::GET,
            __METHOD__,
            $tasks
        );

        //====================================================================//
        // Verify Response
        $this->verifyResponses($objectType, $responses, $verify);
    }

    /**
     * Verify Object Multi-read Response
     */
    public function verifyResponses(string $objectType, mixed $responses, bool $verify = true): void
    {
        Assert::assertIsArray($responses);
        //====================================================================//
        // Walk on Expected Object IDs
        foreach ($this->objectIds as $objectId) {
            //====================================================================//
            // Extract Next Response
            /** @var null|array|scalar $response */
            $response = array_shift($responses);
            //====================================================================//
            // Verify Response
            $this->verifyResponse($objectType, $objectId, $response, $verify);
        }
    }

    /**
     * Verify Object Read Response
     */
    public function verifyResponse(string $objectType, string $objectId, $response, bool $verify = true): void
    {
        Assert::assertNotEmpty($objectType);
        //====================================================================//
        // Verify Response Block
        $actual = ObjectReadValidator::assertValidResponse($response, $objectId);
        //====================================================================//
        // Store ID of Last Tested Object on Context
        TestContext::setObjectId($objectId);
        //====================================================================//
        // No Verify => Exit
        if (!$verify) {
            return;
        }
        //====================================================================//
        // Get Expected Dataset
        $expected = $this->objectsDatasets[$objectId] ?? null;
        Assert::assertIsArray($expected);
        //====================================================================//
        // Compare Read Data with Expected Data
        $error = ObjectDataComparator::compare(TestContext::fields(), $expected, $actual);
        if ($error) {
            echo PHP_EOL.PHP_EOL.$error;
        }
        Assert::assertTrue(is_null($error), "Object Data is not similar to Expected Data.");
    }
}
