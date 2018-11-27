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

use Splash\Bundle\Models\AbstractConnector;
use Splash\Client\Splash;
use Splash\Local\Local;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @abstract    Splash Bundle Connectors Actions
 */
class ActionsController extends Controller
{
    //====================================================================//
    //   Redirect to Connectors Defined Actions
    //====================================================================//

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
        // Load Connector Manager
        $manager = $this->get('splash.connectors.manager');
        //====================================================================//
        // Seach for This Connection in Local Configuration
        $serverId = $manager->hasWebserviceConfiguration($webserviceId);
        //====================================================================//
        // Safety Check
        if (!$serverId) {
            //====================================================================//
            // Return Empty Response
            return new Response('This WebService Provide no Description.');
        }
        $connector = $manager->get($webserviceId, $this->getDataBaseConfiguration($serverId));
        //====================================================================//
        // Safety Check
        if (!($controllerAction = $this->validate($connector, $connectorName, $action))) {
            //====================================================================//
            // Return Empty Response
            return new Response('This WebService Provide no Description.');
        }
        //====================================================================//
        // Setup Php Specific Settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR);
        define('SPLASH_SERVER_MODE', 1);
        //====================================================================//
        // Setup Local Splash Module for Current Server
        /** @var Local $local */
        $local = Splash::local();
        $local->setServerId($serverId);
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
        //====================================================================//
        // Redirect to Requested Conroller Action
        try {
            $response = $this->forward($controllerAction, array('Connector' => $connector));
        } catch (\InvalidArgumentException $e) {
            //====================================================================//
            // Return Empty Response
            return new Response($e->getMessage());
        }

        return $response;
    }

    /**
     * @abstract    Get Server Stored Configuration
     *
     * @param string $serverId
     *
     * @return array
     */
    public function getDataBaseConfiguration(string $serverId)
    {
        //====================================================================//
        // Load Configuration from DataBase if Exists
        $dbConfig = $this->getDoctrine()->getRepository('AppExplorerBundle:SplashServer')->findOneByIdentifier($serverId);
        //====================================================================//
        // Return Configuration
        if (empty($dbConfig)) {
            return  array();
        }

        return $dbConfig->getSettings();
    }

    /**
     * @abstract    Validate Controller Action Request
     *
     * @param AbstractConnector $connector
     * @param string            $connectorName
     * @param string            $action
     *
     * @return false|string
     */
    public function validate(AbstractConnector $connector = null, string $connectorName = null, string $action = null)
    {
        //====================================================================//
        // Safety Check - Connector Exists
        if (!$connector || !$action) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Name is Similar
        $profile = $connector->getProfile();
        if (!isset($profile['name']) || (strtolower((string) $connectorName) != strtolower($profile['name']))) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Action Exists
        $connectorActions = $connector->getAvailableActions();
        if (!isset($connectorActions[strtolower($action)]) || empty($connectorActions[strtolower($action)])) {
            return false;
        }
        //====================================================================//
        // Safety Check - Action Controller Exists
        // TODO

        return $connectorActions[strtolower($action)];
    }
}
