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

namespace Splash\Bundle\Dictionary;

/**
 * Dictionary for Splash Bundle Webservice Routes
 */
class SplashBundleRoutes
{
    /**
     * Main SOAP Access
     */
    const SOAP = "splash_main_soap";

    /**
     * Main SOAP Connect & Test
     */
    const SOAP_TEST = "splash_test_soap";

    /**
     * Connector Master Action
     */
    const MASTER = "splash_connector_action_master";

    /**
     * Connector Public Action
     */
    const PUBLIC = "splash_connector_action";

    /**
     * Connector Secured Action
     */
    const SECURED = "splash_connector_secured_action";
}
