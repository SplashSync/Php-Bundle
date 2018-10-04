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
 **/

namespace Splash\Bundle\Models\Connectors;

use ArrayObject;

/**
 * @abstract Connector Interface define Required structure for Communication Connectors Profile 
 */
interface ProfileInterface {

    //  Connector Types Names   
    const TYPE_SERVER       = "Server";
    const TYPE_ACCOUNT      = "Account";
    const TYPE_HIDDEN       = "Hidden";
    
    //  Connector Default Profile Array   
    const DEFAULT_PROFILE   = array(
        'enabled'   =>      True,                                   // is Connector Enabled
        'beta'      =>      True,                                   // is this a Beta release
        'type'      =>      self::TYPE_SERVER,                      // Connector Type or Mode                
        'name'      =>      '',                                     // Connector code (lowercase, no space allowed) 
        'connector' =>      '',                                     // Connector PUBLIC service
        'title'     =>      '',                                     // Public short name
        'label'     =>      '',                                     // Public long name
        'domain'    =>      False,                                  // Translation domain for names
        'ico'       =>      '/bundles/splash/img/Splash-ico.png',   // Public Icon path
        'www'       =>      'www.splashsync.com',                   // Website Url
    );
    
    /**
     * @abstract   Get Connector Profile Informations
     * @return  array
     */    
    public function getProfile() : array;    
    
    /**
     * @abstract   Get Connector Profile Twig Template Name
     * 
     * @return  string 
     */    
    public function getProfileTemplate() : string;
    
    /**
     * @abstract   Get Connector Form Builder Class
     * 
     * @return  string 
     */    
    public function getFormBuilderName() : string;

    /**
     * @abstract   Get Connector Availables Controller Actions
     * 
     * @return  ArrayObject
     */    
    public function getAvailableActions() : ArrayObject;
    
}
