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

namespace Splash\Tests\WsObjects;

use Exception;
use PHPUnit\Framework\Assert;
use Splash\Core\Fields\ObjectField;
use Splash\Validator\Phpunit\TestContext;
use Splash\Validator\Phpunit\Tests\ObjectCrudTest;
use Splash\Bundle\Phpunit\Tests\Read\ObjectMultiReadTest;
use Splash\Validator\Phpunit\TestSequences;
use Splash\Validator\SplashTestCase;

/**
 * Connector Specific Tests Suite - Multiple Objects Reading at Once
 */
class C001ObjectsGetMultiTest extends SplashTestCase
{
    const MAX_ITEMS = 2;

    /**
     * Verify Reading of Multiple Objects at Once from Module
     *
     * @dataProvider objectTestableFieldsProvider
     *
     * @throws Exception
     */
    public function testGetMultipleFromConnector(string $sequence, string $objectType, ObjectField $field): void
    {
        $this->testGetMultiple($sequence, $objectType, $field);
    }

    /**
     * Verify Reading of Multiple Objects from Service
     *
     * @dataProvider objectTestableFieldsProvider
     *
     * @throws Exception
     */
    public function testGetMultipleFromService(string $sequence, string $objectType, ObjectField $field): void
    {
        $this->testGetMultiple($sequence, $objectType, $field, true);
    }

    /**
     * Verify Reading of Multiple Objects at Once
     *
     * @dataProvider objectTestableFieldsProvider
     *
     * @throws Exception
     */
    protected static function testGetMultiple(string $sequence, string $objectType, ObjectField $field, bool $useService = false): void
    {
        $objectIds = $objectDatasets = array();
        //====================================================================//
        // Configure Env. for Test Sequence
        TestSequences::configure($sequence);
        //====================================================================//
        // CREATE MULTIPLE OBJECTS & STORE THEM IN LOCAL ARRAY
        //====================================================================//
        $objectTest = new ObjectCrudTest($sequence, $objectType);
        $objectTest->useServiceMethods($useService);
        for ($i = 0; $i < self::MAX_ITEMS; $i++) {
            //====================================================================//
            // Execute Write Test
            $objectTest->executeWriteTest($field);
            //====================================================================//
            // Store Object Id and Data
            $objectId = TestContext::objectId();
            Assert::assertNotEmpty($objectId, "Object Id is Empty");
            $objectIds[] = $objectId;
            $objectDatasets[$objectId] = TestContext::dataset();
            TestContext::setObjectId(null);
        }
        //====================================================================//
        // READ MULTIPLE OBJECTS AT ONCE & VERIFY RESPONSE
        //====================================================================//
        $objectTest
            ->setReadTest(new ObjectMultiReadTest($objectIds, $objectDatasets))
            ->executeReadTest($field)
        ;
    }
}
