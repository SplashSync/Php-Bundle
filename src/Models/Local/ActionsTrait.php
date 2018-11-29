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

namespace Splash\Bundle\Models\Local;

use InvalidArgumentException;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Core\SplashCore  as Splash;
use Splash\Local\Local;
use Symfony\Component\HttpFoundation\Response;

/**
 * @abstract    Splash Bundle Controller Actions Functions
 */
trait ActionsTrait
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultResponse()
    {
        return new Response('This WebService Provide no Description.');
    }

    /**
     * @abstract    Setup Php Specific Settings
     */
    public static function setupPhpOptions()
    {
        ini_set('display_errors', 0);
        error_reporting(E_ERROR);
        define('SPLASH_SERVER_MODE', 1);
    }

    /**
     * @abstract    Setup Local Splash Module for Current Server
     *
     * @param string $serverId Registerd Server Id
     */
    public static function setupServerId(string $serverId)
    {
        //====================================================================//
        // Setup Local Splash Module for Current Server
        /** @var Local $local */
        $local = Splash::local();
        $local->setServerId($serverId);
        //====================================================================//
        // Reboot Splash Core Module
        Splash::reboot();
    }

    /**
     * @abstract    Validate Connector Action Exists
     *
     * @param string $webserviceId
     *
     * @return AbstractConnector|false
     */
    public function getConnectorFromManager(string $webserviceId)
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
            return false;
        }
        $connector = $manager->get($webserviceId);
        //====================================================================//
        // Safety Check
        if (!$connector) {
            return false;
        }
        //====================================================================//
        // Setup Php Specific Settings
        self::setupPhpOptions();
        //====================================================================//
        // Setup Local Splash Module for Current Server
        self::setupServerId($serverId);

        return $connector;
    }

    /**
     * @abstract    Validate Connector Action Exists
     *
     * @param AbstractConnector $connector
     * @param string            $connectorName
     * @param string            $action
     *
     * @return false|string
     */
    public static function hasConnectorAction(AbstractConnector $connector, string $connectorName, string $action)
    {
        //====================================================================//
        // Safety Check - Connector Exists
        if (empty($connector) || empty($action)) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Name is Similar
        $profile = $connector->getProfile();
        if (!isset($profile['name']) || (strtolower($connectorName) != strtolower($profile['name']))) {
            return false;
        }
        //====================================================================//
        // Safety Check - Connector Action Exists
        $connectorActions = $connector->getAvailableActions();
        if (!isset($connectorActions[strtolower($action)]) || empty($connectorActions[strtolower($action)])) {
            return false;
        }

        return $connectorActions[strtolower($action)];
    }

    /**
     * @abstract    Validate Connector Action Exists
     *
     * @param string            $controller
     * @param AbstractConnector $connector
     *
     * @return Response
     */
    public function forwardToConnector(string $controller, AbstractConnector $connector)
    {
        //====================================================================//
        // Safety Check
        if (empty($connector) || empty($controller)) {
            return self::getDefaultResponse();
        }
        //====================================================================//
        // Redirect to Requested Controller Action
        try {
            $response = $this->forward($controller, array('connector' => $connector));
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage());
        }

        return $response;
    }
}
