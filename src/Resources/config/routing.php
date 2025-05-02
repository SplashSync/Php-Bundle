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

namespace Splash\Bundle\Resources\config;

use Splash\Bundle\Controller\ActionsController;
use Splash\Bundle\Controller\SoapController;
use Splash\Bundle\Dictionary\SplashBundleRoutes;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    //====================================================================//
    // Main SOAP Access
    $routes
        ->add(SplashBundleRoutes::SOAP, '/splash')
        ->controller(array(SoapController::class, "mainAction"))
    ;
    //====================================================================//
    // Main SOAP Connect & Test
    $routes
        ->add(SplashBundleRoutes::SOAP_TEST, '/splash-test')
        ->controller(array(SoapController::class, "testAction"))
    ;
    //====================================================================//
    // Execute Connector Master Actions
    $routes
        ->add(SplashBundleRoutes::MASTER, '/{connectorName}')
        ->controller(array(ActionsController::class, "masterAction"))
    ;
    //====================================================================//
    // Execute Connectors Public Actions
    $routes
        ->add(SplashBundleRoutes::PUBLIC, '/{connectorName}/{webserviceId}/{action}')
        ->controller(array(ActionsController::class, "publicAction"))
        ->defaults(array("action" => "index"))
    ;
    //====================================================================//
    // Execute Connectors Secured Actions
    $routes
        ->add(SplashBundleRoutes::SECURED, '/{connectorName}/{webserviceId}/secured/{action}')
        ->controller(array(ActionsController::class, "securedAction"))
    ;
};
