<?php

namespace Splash\Bundle\Phpunit\Providers;

use Exception;

/**
 * Connectors Datasets Provider for Splash Phpunit Test Cases
 */
trait ConnectorsProviderTrait
{
    /**
     * Data Provider: Tests Sequences + Splash Server ID
     *
     * @return array<string, array>
     *
     * @throws Exception
     */
    public function serverIdProvider(): array
    {
        $result = array();
        //====================================================================//
        // Boot Test Environment
        self::setUp();
        //====================================================================//
        // Walk on Defined Servers
        $manager = $this->getConnectorsManager();
        foreach ($manager->getServersNames() as $serverId => $serverName) {
            //====================================================================//
            // Add Server to List
            $dataSetName = '['.$serverId."] ".$serverName;
            $result[$dataSetName] = array(
                'sequence' => $serverName,
                'serverId' => $serverId
            );
        }
        //====================================================================//
        // Stop Test Environment
        self::tearDown();

        return $result;
    }
}