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

namespace Splash\Bundle\Controller;

use Splash\Bundle\Models\Local\ActionsTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Splash Bundle Connectors Actions
 */
class ActionsController extends Controller
{
    use ActionsTrait;
    
    //====================================================================//
    //   Redirect to Connectors Defined Actions
    //====================================================================//

    /**
     * Redirect to Connectors Defined Actions
     *
     * @param string $connectorName
     *
     * @return Response
     */
    public function masterAction(string $connectorName)
    {
        //====================================================================//
        // Seach for This Connection in Local Configuration
        $connector = $this->get('splash.connectors.manager')->getRawConnector($connectorName);
        //====================================================================//
        // Safety Check => Connector Exists
        if (!$connector) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Safety Check => Master Action Exists
        $controllerAction = $connector->getMasterAction();
        if (!$controllerAction) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Conroller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }

    /**
     * Redirect to Connectors Public Actions
     *
     * @param string $connectorName
     * @param string $webserviceId
     * @param string $action
     *
     * @return Response
     */
    public function publicAction(string $connectorName, string $webserviceId, string $action)
    {
        //====================================================================//
        // Seach for This Connector in Local Configuration
        $connector = $this->getConnectorFromManager($webserviceId);
        //====================================================================//
        // Safety Check => Connector Exists
        if (!$connector) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Safety Check => Action Exists
        if (!($controllerAction = self::hasPublicAction($connector, $connectorName, $action))) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Conroller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }
    
    /**
     * Redirect to Connectors Secured/Private Actions
     *
     * @param string $connectorName
     * @param string $webserviceId
     * @param string $action
     *
     * @return Response
     */
    public function securedAction(string $connectorName, string $webserviceId, string $action)
    {
        //====================================================================//
        // Seach for This Connector in Local Configuration
        $connector = $this->getConnectorFromManager($webserviceId);
        //====================================================================//
        // Safety Check => Connector Exists
        if (!$connector) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Safety Check => Action Exists
        $controllerAction = self::hasSecuredAction($connector, $connectorName, $action);
        if (!$controllerAction) {
            return self::getDefaultResponse();
        }
//        //====================================================================//
//        // Redirect to Requested Conroller Action
//        return $this->forwardToConnector($controllerAction, $connector);
        //====================================================================//
        // NO Secured Actions for Symfony Internal Connector
        // Whatever, we skip the Action Redirect
        return self::getDefaultResponse();
    }
}
