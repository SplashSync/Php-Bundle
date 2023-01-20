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

namespace Splash\Bundle\Models\Manager;

use Splash\Core\SplashCore as Splash;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * @abstract    Symfony Session Manager for Splash Connectors Manager
 */
trait SessionTrait
{
    /**
     * @var null|SessionInterface
     */
    private ?SessionInterface $session = null;

    /**
     * @var null|AuthorizationCheckerInterface
     */
    private ?AuthorizationCheckerInterface $authChecker = null;

    /**
     * Push Splash Log to Symfony Session
     *
     * @param bool $clean Clean Log after Display
     */
    public function pushLogToSession(bool $clean): void
    {
        $flashesBag = $this->getFlashBag();
        //====================================================================//
        // Decide if Current Logged User Needs to Be Notified or Not
        if (!$flashesBag || !$this->isAllowedNotify()) {
            return;
        }
        //====================================================================//
        // Catch Splash Errors
        foreach (Splash::log()->err as $message) {
            $flashesBag->add('error', $message);
        }
        //====================================================================//
        // Catch Splash Warnings
        foreach (Splash::log()->war as $message) {
            $flashesBag->add('warning', $message);
        }
        //====================================================================//
        // Catch Splash Messages
        foreach (Splash::log()->msg as $message) {
            $flashesBag->add('success', $message);
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
            $roles = $this->getCoreParameter('notify');
            //====================================================================//
            // Walk on User Allowed Roles
            foreach (is_iterable($roles) ? $roles : array() as $notifyRole) {
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
     * @param null|SessionInterface $session
     *
     * @return $this
     */
    protected function setSession(?SessionInterface $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Store Symfony Auth Checker
     *
     * @param null|AuthorizationCheckerInterface $authChecker
     *
     * @return $this
     */
    protected function setAuthorizationChecker(?AuthorizationCheckerInterface $authChecker): self
    {
        $this->authChecker = $authChecker;

        return $this;
    }

    /**
     * Get Symfony Session Flashes Bag
     *
     * @return null|FlashBagInterface
     */
    private function getFlashBag(): ?FlashBagInterface
    {
        try {
            if ($this->session) {
                $bag = $this->session->getBag("flashes");

                return ($bag instanceof FlashBagInterface) ? $bag : null;
            }

            return null;
        } catch (\Throwable $ex) {
            return null;
        }
    }
}
