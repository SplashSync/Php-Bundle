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

namespace Splash\Bundle\Models\Local;

use Exception;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Objects\Manager;
use Splash\Models\Objects\ObjectInterface;

/**
 * Splash Bundle Local Class Objects Functions
 */
trait ObjectsTrait
{
    /**
     * @var array
     */
    private array $objectManagers = array();

    /**
     * {@inheritdoc}
     */
    public function objects(): array
    {
        //====================================================================//
        // Load Objects Type List
        try {
            return $this->getConnector()->getAvailableObjects();
        } catch (Exception $ex) {
            Splash::log()->report($ex);

            return array();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function object(string $objectType): ObjectInterface
    {
        //====================================================================//
        // Build Objects Type Index Key
        $index = get_class($this->getConnector())."::".$objectType;
        //====================================================================//
        // If Object Manager is New
        if (!isset($this->objectManagers[$index])) {
            $this->objectManagers[$index] = new Manager($this->getConnector(), $objectType);
        }

        //====================================================================//
        // Return Object Manager
        return $this->objectManagers[$index];
    }
}
