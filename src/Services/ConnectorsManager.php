<?php
/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */


namespace Splash\Bundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;

use Splash\Bundle\Models\Manager\ConfigurationTrait;
use Splash\Bundle\Models\Manager\ConnectorsTrait;
use Splash\Bundle\Models\Manager\SessionTrait;
use Splash\Bundle\Models\Manager\ObjectsEventsTrait;

/**
 * @abstract Splash Bundle Connectors Manager
 */
class ConnectorsManager
{

    use ConfigurationTrait;
    use ConnectorsTrait;
    use SessionTrait;
    use ObjectsEventsTrait;
    
    public function __construct(
        array $Config,                  // Splash Bundle Core Configuration
        $TaggedConnectors,              // Tagged Connectors Services
        Session $Session                // Symfony Session Service
    ) {
        //====================================================================//
        // Store Splash Bundle Core Configuration
        $this->setCoreConfiguration($Config);
        //====================================================================//
        // Register Tagged Splash Connector Services
        foreach ($TaggedConnectors as $Connector) {
            $this->registerConnectorService($Connector);
        }
        //====================================================================//
        // Setup Session
        $this->setSession($Session);
    }
}
