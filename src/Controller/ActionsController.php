<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
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
 * @abstract    Splash Bundle Connectors Actions
 */
class ActionsController extends Controller
{
    use ActionsTrait;
    
    //====================================================================//
    //   Redirect to Connectors Defined Actions
    //====================================================================//

    /**
     * @abstract    Redirect to Connectors Defined Actions
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
        // Safety Check => Action Exists
        if (!($controllerAction = self::hasConnectorAction($connector, $connectorName, "master"))) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Conroller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }

    /**
     * @abstract    Redirect to Connectors Defined Actions
     *
     * @param string $connectorName
     * @param string $webserviceId
     * @param string $action
     *
     * @return Response
     */
    public function indexAction(string $connectorName, string $webserviceId, string $action)
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
        if (!($controllerAction = self::hasConnectorAction($connector, $connectorName, $action))) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Conroller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }
}
