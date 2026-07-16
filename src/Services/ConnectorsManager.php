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

namespace Splash\Bundle\Services;

use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Splash\Bundle\Models\Manager\CacheTrait;
use Splash\Bundle\Models\Manager\ConfigurationTrait;
use Splash\Bundle\Models\Manager\ConnectorsTrait;
use Splash\Bundle\Models\Manager\GetFileEventsTrait;
use Splash\Bundle\Models\Manager\IdentifyEventsTrait;
use Splash\Bundle\Models\Manager\ObjectsEventsTrait;
use Splash\Bundle\Models\Manager\SessionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Splash Bundle Connectors Manager
 */
class ConnectorsManager
{
    use CacheTrait;
    use ConfigurationTrait;
    use ConnectorsTrait;
    use SessionTrait;
    use ObjectsEventsTrait;
    use GetFileEventsTrait;
    use IdentifyEventsTrait;

    /**
     * Service Constructor
     *
     * @param array                              $config           Splash Configuration Array
     * @param iterable                           $taggedConnectors Tagged Splash Connectors
     * @param RequestStack                       $requestStack     Symfony Request Stack
     * @param null|AuthorizationCheckerInterface $authChecker      Symfony Security Checker Interface
     *
     * @throws Exception
     */
    public function __construct(
        array $config,
        iterable $taggedConnectors,
        CacheItemPoolInterface $cache,
        RequestStack $requestStack,
        ?AuthorizationCheckerInterface $authChecker
    ) {
        //====================================================================//
        // Store Splash Bundle Core Configuration
        $this->setCoreConfiguration($config);
        //====================================================================//
        // Register Tagged Splash Connector Services
        foreach ($taggedConnectors as $connector) {
            $this->registerConnectorService($connector);
        }
        //====================================================================//
        // Setup Caching
        $this->setCache($cache);
        //====================================================================//
        // Setup Session
        $this->setRequestStack($requestStack);
        $this->setAuthorizationChecker($authChecker);
    }
}
