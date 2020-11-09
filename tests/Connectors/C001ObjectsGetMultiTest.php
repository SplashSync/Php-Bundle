<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2020 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Tests\Connectors;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Local\Local;
use Splash\Tests\Tools\ObjectsCase;
use Splash\Tests\Tools\Traits\ObjectsSetTestsTrait;

/**
 * Connectors Spécific Tests Suite - Multiple Objects Reading
 *
 * @author SplashSync <contact@splashsync.com>
 */
class C001ObjectsGetMultiTest extends ObjectsCase
{
    use ObjectsSetTestsTrait;
    const MAX_ITEMS = 2;

    /**
     * @var array
     */
    private $objectsData = array();

    /**
     * Verify reading of Multiple Objects from Module
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string      $testSequence
     * @param string      $objectType
     * @param ArrayObject $field
     *
     * @return void
     */
    public function testGetMultipleFromConnector(string $testSequence, string $objectType, ArrayObject $field): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   CREATE MULTIPLE OBJECTS
        //====================================================================//

        for ($i = 0; $i < self::MAX_ITEMS; $i++) {
            //====================================================================//
            //   Generate Dummy Object Data (Required Fields Only)
            $newData = $this->prepareForTesting($objectType, $field);
            if (false == $newData) {
                return;
            }
            //====================================================================//
            //   Execute Create Test
            $objectId = $this->setObjectFromModule($objectType, $newData);
            //====================================================================//
            //   Store Objects Data
            $this->objectsData[$objectId] = $newData;
        }

        //====================================================================//
        // BOOT or REBOOT MODULE
        $this->setUp();

        //====================================================================//
        //   READ MULTIPLE OBJECTS
        //====================================================================//

        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList($this->fields, true, false);
        //====================================================================//
        //   Execute Action Directly on Connector
        /** @var Local $local */
        $local = Splash::local();
        $data = $local->getConnector()->getObject($objectType, array_keys($this->objectsData), $fields);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $data);
    }

    /**
     * Verify reading of Multiple Objects from Service
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string      $testSequence
     * @param string      $objectType
     * @param ArrayObject $field
     *
     * @return void
     */
    public function testGetMultipleFromService(string $testSequence, string $objectType, ArrayObject $field): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   CREATE MULTIPLE OBJECTS
        //====================================================================//

        for ($i = 0; $i < self::MAX_ITEMS; $i++) {
            //====================================================================//
            //   Generate Dummy Object Data (Required Fields Only)
            $newData = $this->prepareForTesting($objectType, $field);
            if (false == $newData) {
                return;
            }
            //====================================================================//
            //   Execute Create Test
            $objectId = $this->setObjectFromService($objectType, $newData);
            //====================================================================//
            //   Store Objects Data
            $this->objectsData[$objectId] = $newData;
        }

        //====================================================================//
        // BOOT or REBOOT MODULE
        $this->setUp();

        //====================================================================//
        //   READ MULTIPLE OBJECTS
        //====================================================================//

        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList($this->fields, true, false);

        //====================================================================//
        //   Build Multiple Read Tasks List
        $tasks = array();
        foreach (array_keys($this->objectsData) as $index => $readObjectid) {
            $tasks[$index] = array( "type" => $objectType, "id" => $readObjectid, "fields" => $fields);
        }
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->multipleAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, $tasks);

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $data);
    }

    /**
     * Verify Multiple Get Response
     *
     * @param string $objectType
     * @param mixed  $data
     *
     * @return void
     */
    protected function verifyResponse($objectType, $data): void
    {
        //====================================================================//
        //   Verify Response Block
        $this->assertNotEmpty($data, "Data Block is Empty");
        $this->assertIsArray($data, "Data Block is Not an Array");
        $this->assertEquals(self::MAX_ITEMS, count($data), "Returned Objects Data count is Different");

        //====================================================================//
        //  Verify Object Data
        foreach ($this->objectsData as $objectId => $objectData) {
            //====================================================================//
            //  Response Object Id
            $response = array_shift($data);
            $this->assertArrayHasKey("id", $response, "Returned Data has no Object Id inside");
            $this->assertEquals($objectId, $response['id'], "Returned Object Id is different");
            unset($response['id']);
            //====================================================================//
            //   Verify Object Data are Ok
            $this->compareDataBlocks($this->fields, $objectData, $response, $objectType);
        }
    }
}
