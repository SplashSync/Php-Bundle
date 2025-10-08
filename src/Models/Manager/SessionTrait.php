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

use Splash\Core\Client\Splash;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Throwable;

/**
 * Symfony Session Manager for Splash Connectors Manager
 */
trait SessionTrait
{
    /**
     * Symfony Request Stack, used to get Current Session
     */
    private RequestStack $requestStack;

    /**
     * @var null|FlashBagInterface
     */
    private ?FlashBagInterface $flashBag = null;

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
     * Store Symfony Request Stack
     */
    protected function setRequestStack(RequestStack $requestStack): static
    {
        $this->requestStack = $requestStack;

        return $this;
    }

    /**
     * Store Symfony Auth Checker
     */
    protected function setAuthorizationChecker(?AuthorizationCheckerInterface $authChecker): static
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
        //====================================================================//
        // Flash Bag Already Loaded
        if (isset($this->flashBag)) {
            return $this->flashBag;
        }

        try {
            //====================================================================//
            // Get Session from Request Stack
            // Exception is Thrown if Session is not Available
            $flashBag = $this->requestStack->getSession()->getBag("flashes");

            return ($flashBag instanceof FlashBagInterface) ? $flashBag : null;
        } catch (Throwable) {
            return null;
        }
    }
}
