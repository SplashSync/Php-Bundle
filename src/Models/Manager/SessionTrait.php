<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Manager;

use Splash\Core\SplashCore as Splash;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * @abstract    Symfony Session Manager for Spash Connectors Manager
 */
trait SessionTrait
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var AuthorizationChecker
     */
    private $authChecker;

    /**
     * Push Splash Log to Symfoiny Session
     *
     * @param bool $clean Clean Log after Display
     */
    public function pushLogToSession(bool $clean): void
    {
        //====================================================================//
        // Decide if Current Logged User Needs to Be Notified or Not
        if (!$this->isAllowedNotify()) {
            return;
        }
        //====================================================================//
        // Catch Splash Errors
        if (!empty(Splash::log()->err)) {
            foreach (Splash::log()->err as $message) {
                $this->session->getFlashBag()->add('error', $message);
            }
        }
        //====================================================================//
        // Catch Splash Warnings
        if (!empty(Splash::log()->war)) {
            foreach (Splash::log()->war as $message) {
                $this->session->getFlashBag()->add('warning', $message);
            }
        }
        //====================================================================//
        // Catch Splash Messages
        if (!empty(Splash::log()->msg)) {
            foreach (Splash::log()->msg as $message) {
                $this->session->getFlashBag()->add('success', $message);
            }
        }
        //====================================================================//
        // Clear Splash Log
        if ($clean) {
            Splash::log()->cleanLog();
        }
    }

    /**
     * Decide if Current Logged User Needs to Be Notified or Not
     *
     * @return bool
     */
    public function isAllowedNotify(): bool
    {
        //====================================================================//
        // Safety Check
        if (!isset($this->authChecker)) {
            return true;
        }

        try {
            //====================================================================//
            // Walk on User Allowed Roles
            foreach ($this->getCoreParameter('notify') as $notifyRole) {
                //====================================================================//
                // User as Role => Notifications Allowed
                if ($this->authChecker->isGranted($notifyRole)) {
                    return true;
                }
            }
        } catch (AuthenticationCredentialsNotFoundException $exc) {
            //====================================================================//
            // Notifications Not Allowed
            return false;
        }

        return false;
    }

    /**
     * Store Symfony Session
     *
     * @param Session $session
     *
     * @return $this
     */
    private function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Store Symfony Auth Checker
     *
     * @param AuthorizationChecker $authChecker
     *
     * @return $this
     */
    private function setAuthorizationChecker(AuthorizationChecker $authChecker)
    {
        $this->authChecker = $authChecker;

        return $this;
    }
}
