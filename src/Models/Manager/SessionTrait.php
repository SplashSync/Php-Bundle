<?php
/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */

namespace Splash\Bundle\Models\Manager;

use Symfony\Component\HttpFoundation\Session\Session;

use Splash\Core\SplashCore as Splash;

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
     * @abstract    Set Splash Bundle Core Configuration
     *
     * @param   Session $session
     *
     * @return  $this
     */
    private function setSession(Session $session)
    {
        $this->session  =   $session;

        return $this;
    }

    /**
     * @abstract    Push Splash Log to Symfoiny Session
     *
     * @param   bool $clean Clean Log after Display
     *
     * @return  $this
     */
    public function pushLogToSession(bool $clean)
    {
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
}
