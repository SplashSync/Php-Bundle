<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Services;

use Splash\Bundle\Models\Manager\ConfigurationTrait;
use Splash\Bundle\Models\Manager\ConnectorsTrait;
use Splash\Bundle\Models\Manager\GetFileEventsTrait;
use Splash\Bundle\Models\Manager\IdentifyEventsTrait;
use Splash\Bundle\Models\Manager\ObjectsEventsTrait;
use Splash\Bundle\Models\Manager\SessionTrait;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Splash Bundle Connectors Manager
 */
class ConnectorsManager
{
    use ConfigurationTrait;
    use ConnectorsTrait;
    use SessionTrait;
    use ObjectsEventsTrait;
    use GetFileEventsTrait;
    use IdentifyEventsTrait;

    /**
     * Service Constructor
     *
     * @param array                $config
     * @param array                $taggedConnectors
     * @param Session              $session
     * @param AuthorizationChecker $authChecker
     */
    public function __construct(array $config, $taggedConnectors, Session $session, AuthorizationChecker $authChecker)
    {
        //====================================================================//
        // Store Splash Bundle Core Configuration
        $this->setCoreConfiguration($config);
        //====================================================================//
        // Register Tagged Splash Connector Services
        foreach ($taggedConnectors as $connector) {
            $this->registerConnectorService($connector);
        }
        //====================================================================//
        // Setup Session
        $this->setSession($session);
        $this->setAuthorizationChecker($authChecker);
    }
}
