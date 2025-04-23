<?php

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