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

use Splash\Bundle\Controller\ActionsController;
use Splash\Bundle\Controller\SoapController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    //==============================================================================
    // NODES SOAP & NUSOAP WEBSERVICE ROUTES
    //==============================================================================

    // Main SOAP Access
    $routes->add('splash_main_soap', '/splash')
        ->controller(array(SoapController::class, 'mainAction'))
    ;
    // Connect & Test
    $routes->add('splash_test_soap', '/splash-test')
        ->controller(array(SoapController::class, 'testAction'))
    ;

    //==============================================================================
    // CONNECTORS ACTION ROUTES
    //==============================================================================

    // Execute Connector Master Actions
    $routes->add('splash_connector_action_master', '/{connectorName}')
        ->controller(array(ActionsController::class, 'masterAction'))
    ;
    // Execute Connectors Public Actions
    $routes->add('splash_connector_action', '/{connectorName}/{webserviceId}/{action}')
        ->controller(array(ActionsController::class, 'publicAction'))
        ->defaults(array('action' => 'index'))
    ;
    // Execute Connectors Secured Actions
    $routes->add('splash_connector_secured_action', '/{connectorName}/{webserviceId}/secured/{action}')
        ->controller(array(ActionsController::class, 'securedAction'))
    ;
};
