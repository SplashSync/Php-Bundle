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

use Splash\Bundle\Models\Connectors\ConnectorInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\EventDispatcher\GenericEvent;



use Splash\Bundle\Models\Manager\ConfigurationTrait;
use Splash\Bundle\Models\Manager\ConnectorsTrait;
//use Splash\Bundle\Models\Manager\WebserviceTrait;
use Splash\Bundle\Models\Manager\ObjectsEventsTrait;

/**
 * @abstract Splash Bundle Connectors Manager
 */
class ConnectorsManager
{

    use ConfigurationTrait;
    use ConnectorsTrait;
//    use WebserviceTrait;
    use ObjectsEventsTrait;
    
    public function __construct(
        array $Config,                  // Splash Bundle Core Configuration
        $TaggedConnectors               // Tagged Connectors Services
    ) {
        //====================================================================//
        // Store Splash Bundle Core Configuration
        $this->setCoreConfiguration($Config);
        //====================================================================//
        // Register Tagged Splash Connector Services
        foreach ($TaggedConnectors as $Connector) {
            $this->registerConnectorService($Connector);
        }
    }
}
