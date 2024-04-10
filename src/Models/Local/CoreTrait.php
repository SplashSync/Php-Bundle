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

use ArrayObject;
use Exception;
use Splash\Core\SplashCore as Splash;

/**
 * Splash Bundle Local Class Core Functions
 */
trait CoreTrait
{
    //====================================================================//
    // *******************************************************************//
    //  MANDATORY CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function parameters(): array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        $parameters = array();
        //====================================================================//
        // Safety Check - Server Identify Already Selected
        if (!$this->getServerId()) {
            return $parameters;
        }
        //====================================================================//
        // Server Identification Parameters
        $parameters["WsIdentifier"] = $this->getWebserviceId();
        $parameters["WsEncryptionKey"] = $this->getWebserviceKey();
        //====================================================================//
        // If Expert Mode => Override of Server Host Address
        if (!empty($this->getWebserviceHost())) {
            $parameters["WsHost"] = $this->getWebserviceHost();
        }
        //====================================================================//
        // Setup Server Local Name
        $parameters["localname"] = $this->getServerName();
        //====================================================================//
        // Override Server Host
        if (!empty($this->getServerHost())) {
            $parameters["ServerHost"] = $this->getServerHost();
        }
        //====================================================================//
        // Use of Symfony Routes => Override of Local Server Path Address
        $parameters["ServerPath"] = $this->getServerPath();
        //====================================================================//
        // Multi-Server Mode
        if ($this->isMultiServerMode()) {
            $parameters["WsPostCommit"] = false;
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function includes(): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function selfTest(): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        //  Load Local Translation File
        Splash::Translator()->Load("main@local");
        //====================================================================//
        //  Verify - Server Identifier Given
        if (empty($this->getWebserviceId())) {
            return Splash::log()->err("ErrSelfTestNoWsId");
        }
        //====================================================================//
        //  Verify - Server Encrypt Key Given
        if (empty($this->getWebserviceKey())) {
            return Splash::log()->err("ErrSelfTestNoWsKey");
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function informations(ArrayObject $informations): ArrayObject
    {
        try {
            return $this->getConnector()->informations($informations);
        } catch (Exception $ex) {
            Splash::log()->report($ex);

            return $informations;
        }
    }
}
