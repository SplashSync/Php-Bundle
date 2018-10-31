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
    public function testSequences($Name = null)
    {
        //====================================================================//
        // Load Configured Servers List
        $ServersList    =   $this->getServersNames();
        //====================================================================//
        // Generate Sequence List
        if ($Name == "List") {
            return $ServersList;
        }
        //====================================================================//
        // Identify Server by Name
        if (!in_array($Name, $ServersList)) {
            $this->identify(array_search($Name, $ServersList));
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
        $Parameters       =     array();
        
//        //====================================================================//
//        //  Load Locales Parameters
//        if ($this->getContainer()->hasParameter("locales")) {
//            $Parameters["Langs"] = $this->getContainer()->getParameter("locales");
//        } else {
//            $Parameters["Langs"] = array($this->getContainer()->getParameter("locale"));
//        }
        
        return $Parameters;
    }
}
