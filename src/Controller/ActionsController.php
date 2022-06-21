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

namespace Splash\Bundle\Controller;

use Exception;
use Splash\Bundle\Models\Local\ActionsTrait;
use Splash\Bundle\Services\ConnectorsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Splash Bundle Connectors Actions
 */
class ActionsController extends AbstractController
{
    use ActionsTrait;

    /**
     * @var ConnectorsManager
     */
    private ConnectorsManager $manager;

    /**
     * @param ConnectorsManager $manager Splash Connectors manager
     */
    public function __construct(ConnectorsManager $manager)
    {
        $this->manager = $manager;
    }

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
    public function masterAction(string $connectorName): Response
    {
        //====================================================================//
        // Search for This Connection in Local Configuration
        $connector = $this->manager->getRawConnector($connectorName);
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
        // Redirect to Requested Controller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }

    /**
     * Redirect to Connectors Public Actions
     *
     * @param string $connectorName
     * @param string $webserviceId
     * @param string $action
     *
     * @throws Exception
     *
     * @return Response
     */
    public function publicAction(string $connectorName, string $webserviceId, string $action): Response
    {
        //====================================================================//
        // Search for This Connector in Local Configuration
        $connector = $this->getConnectorFromManager($webserviceId);
        //====================================================================//
        // Safety Check => Connector Exists
        if (!$connector) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Safety Check => Action Exists
        $controllerAction = self::hasPublicAction($connector, $connectorName, $action);
        if (!$controllerAction) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Controller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }

    /**
     * Redirect to Connectors Secured/Private Actions
     *
     * @param string $connectorName
     * @param string $webserviceId
     * @param string $action
     *
     * @throws Exception
     *
     * @return Response
     */
    public function securedAction(string $connectorName, string $webserviceId, string $action): Response
    {
        //====================================================================//
        // NO Secured Actions for Non Connected Users
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //====================================================================//
        // Search for This Connector in Local Configuration
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
        //====================================================================//
        // Redirect to Requested Controller Action
        return $this->forwardToConnector($controllerAction, $connector);
    }
}
