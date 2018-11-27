<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Interfaces\Connectors;

/**
 * @abstract Connector Interface define Required structure for Communication Connectors Profile
 */
interface ProfileInterface
{
    //  Connector Types Names
    const TYPE_SERVER       = "Server";
    const TYPE_ACCOUNT      = "Account";
    const TYPE_HIDDEN       = "Hidden";
    
    //  Connector Default Profile Array
    const DEFAULT_PROFILE   = array(
        'enabled'   =>      true,                                   // is Connector Enabled
        'beta'      =>      true,                                   // is this a Beta release
        'type'      =>      self::TYPE_SERVER,                      // Connector Type or Mode
        'name'      =>      '',                                     // Connector code (lowercase, no space allowed)
        'connector' =>      '',                                     // Connector PUBLIC service
        'title'     =>      '',                                     // Public short name
        'label'     =>      '',                                     // Public long name
        'domain'    =>      false,                                  // Translation domain for names
        'ico'       =>      '/bundles/splash/img/Splash-ico.png',   // Public Icon path
        'www'       =>      'www.splashsync.com',                   // Website Url
    );
    
    /**
     * @abstract   Get Connector Profile Informations
     *
     * @return  array
     */
    public function getProfile() : array;
    
    /**
     * @abstract   Get Connector Profile Template when Connector is Fully Connected
     *
     * @return  string
     */
    public function getConnectedTemplate() : string;
    
    /**
     * @abstract   Get Connector Profile Template when Connector is Offline
     *
     * @return  string
     */
    public function getOfflineTemplate() : string;
    
    /**
     * @abstract   Get Connector Profile Template when Connector is New
     *
     * @return  string
     */
    public function getNewTemplate() : string;
    
    /**
     * @abstract   Get Connector Form Builder Class
     *
     * @return  string
     */
    public function getFormBuilderName() : string;

    /**
     * @abstract   Get Connector Availables Controller Actions
     *
     * @return  array
     */
    public function getAvailableActions() : array;
    
    /**
     * @abstract    Ask for Update of Server Configuration in Memory
     *
     * @return  void
     */
    public function updateConfiguration();
}
