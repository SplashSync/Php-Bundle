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

namespace Splash\Bundle\Models\Local;

use Exception;
use InvalidArgumentException;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Core\Client\Splash;
use Splash\Local\Local;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Splash Bundle Controller Actions Functions
 */
trait ActionsTrait
{
    /**
     * Return Default Empty Connector Response
     *
     * @return Response
     */
    public static function getDefaultResponse(): Response
    {
        return new Response('This WebService Provide no Description.');
    }

    /**
     * Setup Php Specific Settings
     *
     * @return void
     */
    public static function setupPhpOptions(): void
    {
        ini_set('display_errors', "0");
        error_reporting(E_ERROR);
        define('SPLASH_SERVER_MODE', 1);
    }

    /**
     * Setup Local Splash Module for Current Server
     *
     * @param string $serverId Registered Server Id
     *
     * @throws Exception
     *
     * @return void
     */
    public static function setupServerId(string $serverId): void
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
     * Validate Connector Action Exists
     *
     * @param string $webserviceId
     *
     * @throws Exception
     *
     * @return null|AbstractConnector
     */
    public function getConnectorFromManager(string $webserviceId): ?AbstractConnector
    {
        //====================================================================//
        // Search for This Connection in Local Configuration
        $serverId = $this->manager->hasWebserviceConfiguration($webserviceId);
        //====================================================================//
        // Safety Check
        if (!$serverId) {
            return null;
        }
        $connector = $this->manager->get($webserviceId);
        //====================================================================//
        // Safety Check
        if (!$connector) {
            return null;
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
     * Validate Connector Public Action Exists
     *
     * @param AbstractConnector $connector
     * @param string            $connectorName
     * @param string            $action
     *
     * @return false|string
     */
    public static function hasPublicAction(AbstractConnector $connector, string $connectorName, string $action)
    {
        //====================================================================//
        // Safety Check - Connector Exists
        if (empty($action)) {
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
        $connectorActions = $connector->getPublicActions();
        if (!isset($connectorActions[strtolower($action)]) || empty($connectorActions[strtolower($action)])) {
            return false;
        }

        return $connectorActions[strtolower($action)];
    }

    /**
     * Validate Connector Secured Action Exists
     *
     * @param AbstractConnector $connector
     * @param string            $connectorName
     * @param string            $action
     *
     * @return false|string
     */
    public static function hasSecuredAction(AbstractConnector $connector, string $connectorName, string $action)
    {
        //====================================================================//
        // Safety Check - Connector Exists
        if (empty($action)) {
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
        $connectorActions = $connector->getSecuredActions();
        if (!isset($connectorActions[strtolower($action)]) || empty($connectorActions[strtolower($action)])) {
            return false;
        }

        return $connectorActions[strtolower($action)];
    }

    /**
     * Validate Connector Action Exists
     *
     * @param Request           $request
     * @param string            $controller
     * @param AbstractConnector $connector
     *
     * @return Response
     */
    public function forwardToConnector(Request $request, string $controller, AbstractConnector $connector): Response
    {
        //====================================================================//
        // Safety Check
        if (empty($controller)) {
            return self::getDefaultResponse();
        }

        //====================================================================//
        // Load Current Request Query
        $query = $request->query->all();

        //====================================================================//
        // Redirect to Requested Controller Action
        try {
            $response = $this->forward($controller, array('connector' => $connector), $query);
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), 500);
        }

        return $response;
    }
}
