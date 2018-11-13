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
    private $Session;
    
    /**
     * @abstract    Set Splash Bundle Core Configuration
     * @param   Session   $Session
     * @return  $this
     */
    private function setSession(Session $Session)
    {
        $this->Session  =   $Session;
        return $this;
    }

    /**
     * @abstract    Push Splash Log to Symfoiny Session
     * @param   bool    $Clean      Clean Log after Display
     * @return  $this
     */
    public function pushLogToSession(bool $Clean = true)
    {
        //====================================================================//
        // Catch Splash Errors      
        if (!empty(Splash::log()->err)) {
            foreach (Splash::log()->err as $Message) {
                $this->Session->getFlashBag()->add('error', $Message);
            }
        }
        //====================================================================//
        // Catch Splash Warnings      
        if (!empty(Splash::log()->war)) {
            foreach (Splash::log()->war as $Message) {
                $this->Session->getFlashBag()->add('warning', $Message);
            }
        }
        //====================================================================//
        // Catch Splash Messages      
        if (!empty(Splash::log()->msg)) {
            foreach (Splash::log()->msg as $Message) {
                $this->Session->getFlashBag()->add('success', $Message);
            }
        }
        //====================================================================//
        // Clear Splash Log      
        if ($Clean) {
            Splash::log()->cleanLog();
        }
    }
    
}
