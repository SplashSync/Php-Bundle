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

namespace Splash\Bundle\Models\Local;

/**
 * @abstract    Splash Bundle Local Class Tests Functions
 */
trait TestTrait
{
    //====================================================================//
    // *******************************************************************//
    //  OPTIONNAl CORE MODULE LOCAL FUNCTIONS
    // *******************************************************************//
    //====================================================================//
    
    /**
     * {@inheritdoc}
     */
    public function testSequences($name = null)
    {
        //====================================================================//
        // Load Configured Servers List
        $serversList    =   $this->getServersNames();
        //====================================================================//
        // Generate Sequence List
        if ($name == "List") {
            return $serversList;
        }
        //====================================================================//
        // Identify Server by Name
        if (!in_array($name, $serversList)) {
            $this->getManager()->identify((string) array_search($name, $serversList));
        }

        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function testParameters()
    {
        //====================================================================//
        // Init Parameters Array
        $parameters       =     array();
        
//        //====================================================================//
//        //  Load Locales Parameters
//        if ($this->getContainer()->hasParameter("locales")) {
//            $Parameters["Langs"] = $this->getContainer()->getParameter("locales");
//        } else {
//            $Parameters["Langs"] = array($this->getContainer()->getParameter("locale"));
//        }
        
        return $parameters;
    }
}
