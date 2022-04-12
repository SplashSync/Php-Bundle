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

namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;

/**
 * Define Required structure for Administration of Communication Connectors
 */
interface AdminInterface
{
    /**
     * Minimal Test of WebService connection. No Encryption, Just Verify Remote Server is found
     *
     * @return bool
     */
    public function ping() : bool;

    /**
     * Connect WebService and fetch server information
     *
     * @return bool
     */
    public function connect() : bool;

    /**
     * Fetch Server Information
     *
     * @param ArrayObject $informations Information Inputs
     *
     * @return ArrayObject
     */
    public function informations(ArrayObject  $informations) : ArrayObject;

    /**
     * Fetch Server Self Test Results
     *
     * @return bool
     */
    public function selfTest() : bool;
}
