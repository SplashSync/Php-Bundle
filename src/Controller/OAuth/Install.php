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

namespace Splash\Bundle\Controller\OAuth;

use Splash\Bundle\Services\ConnectorsManager;
use Splash\Client\Splash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * TEST ONLY - Try Register User from Connector
 */
class Install extends AbstractController
{
    /**
     * Check Information received for Account/Connector Create
     *
     * @param SessionInterface  $session
     * @param ConnectorsManager $manager
     *
     * @return Response
     */
    public function __invoke(SessionInterface $session, ConnectorsManager $manager): Response
    {
        Splash::log()->cleanLog();

        //==============================================================================
        // Verify Parameters
        /** @var array $requestData */
        $requestData = $session->get(md5(self::class)) ?? array();
        //==============================================================================
        // Verify Received Informations
        $this
            ->verifyUserData($requestData)
            ->verifyConnector($requestData, $manager)
        ;
        //==============================================================================
        // Verify User is Logged In
        $user = $this->getUser();
        if (!$user) {
            Splash::log()->msg("User Already Logged In ".$user);
        } else {
            Splash::log()->war("No User Connected, must be created");
        }

        return new Response(Splash::log()->getHtmlLogList());
    }

    /**
     * Verify Received User Infos
     *
     * @param array $requestData
     *
     * @return self
     */
    private function verifyUserData(array $requestData): self
    {
        //==============================================================================
        // Verify Username
        if (empty($requestData["username"]) || !is_string($requestData["username"])) {
            Splash::log()->err("No username provided, this is required");
        } else {
            Splash::log()->msg("Username found: ".$requestData["username"]);
        }
        //==============================================================================
        // Verify Email
        if (empty($requestData["email"]) || !is_string($requestData["email"])) {
            Splash::log()->err("No email provided, this is required");
        } else {
            Splash::log()->msg("Email found: ".$requestData["email"]);
        }
        //==============================================================================
        // Verify Phone
        if (!empty($requestData["phone"])) {
            if (!is_string($requestData["phone"])) {
                Splash::log()->err("NPhone found, but must be a string");
            } else {
                Splash::log()->msg("Phone found: ".$requestData["phone"]);
            }
        }

        return $this;
    }

    /**
     * Verify Received Connector Infos
     *
     * @param array             $requestData
     * @param ConnectorsManager $manager
     *
     * @return self
     */
    private function verifyConnector(array $requestData, ConnectorsManager $manager): self
    {
        if (empty($requestData["connector"]) || !is_string($requestData["username"])) {
            Splash::log()->err("No connector code received, this is required !");

            return $this;
        }
        if (!$manager->has($requestData["connector"])) {
            Splash::log()->err("Connector code does not exists: ".$requestData["connector"]);
        } else {
            Splash::log()->msg("Connector found: ".$requestData["connector"]);
        }
        if (empty($requestData["configuration"]) || !is_array($requestData["configuration"])) {
            Splash::log()->err("No connector configuration received, this is required !");

            return $this;
        }

        return $this;
    }
}
