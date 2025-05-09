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

namespace Splash\Bundle\Interfaces\Connectors;

/**
 * Connector Interface define Required structure for Communication Connectors Profile
 */
interface ProfileInterface
{
    //====================================================================//
    //  Connector Types Names
    //====================================================================//

    /** @var string */
    const TYPE_CLIENT = "Client";
    /** @var string */
    const TYPE_SERVER = "Server";
    /** @var string */
    const TYPE_ACCOUNT = "Account";
    /** @var string */
    const TYPE_HIDDEN = "Hidden";

    /**
     * Connector Default Profile Array
     *
     * @var array
     */
    const DEFAULT_PROFILE = array(
        'enabled' => true,                              // is Connector Enabled
        'beta' => true,                                 // is this a Beta release
        'type' => self::TYPE_SERVER,                    // Connector Type or Mode
        'name' => '',                                   // Connector code (lowercase, no space allowed)
        'connector' => '',                              // Connector PUBLIC service
        'title' => '',                                  // Public short name
        'label' => '',                                  // Public long name
        'domain' => false,                              // Translation domain for names
        'ico' => '/bundles/splash/img/Splash-ico.png',  // Public Icon path
        'www' => 'www.splashsync.com',                  // Website Url
        'uniqueHost' => false,                          // Require Unique Host Constraint
        'uniqueUrl' => false                            // Require Unique Host+Path pair Constraint
    );

    /**
     * Get Connector Profile Information
     *
     * @return array
     */
    public function getProfile() : array;

    /**
     * Get Connector Profile Template when Connector is Fully Connected
     *
     * @return string
     */
    public function getConnectedTemplate() : string;

    /**
     * Get Connector Profile Template when Connector is Offline
     *
     * @return string
     */
    public function getOfflineTemplate() : string;

    /**
     * Get Connector Profile Template when Connector is New
     *
     * @return string
     */
    public function getNewTemplate() : string;

    /**
     * Get Connector Form Builder Class
     *
     * @return class-string
     */
    public function getFormBuilderName() : string;

    /**
     * Get Connector Master Controller Actions
     * Master Actions may be Accessed by Any Public Users,
     * Webservice ID is not provider (ie Soap Request)
     *
     * @return null|string
     */
    public function getMasterAction(): ?string;

    /**
     * Get Connector Available Public Controller Actions
     * Public Actions may be Accessed by Any Users
     *
     * @return array
     */
    public function getPublicActions() : array;

    /**
     * Get Connector Available Secured Controller Actions
     * Secured Actions Requires User to Be Logged In
     *
     * @return array
     */
    public function getSecuredActions() : array;

    /**
     * Ask for Update of Server Configuration in Memory
     *
     * @return void
     */
    public function updateConfiguration();
}
