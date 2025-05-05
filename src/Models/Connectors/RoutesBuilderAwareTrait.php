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

namespace Splash\Bundle\Models\Connectors;

use Splash\Bundle\Services\ConnectorRoutesBuilder;

/**
 * Manage Routes Builder for Connectors
 */
trait RoutesBuilderAwareTrait
{
    /**
     * @var ConnectorRoutesBuilder
     */
    private ConnectorRoutesBuilder $routeBuilder;

    /**
     * Get Routes Builder
     */
    public function getRouteBuilder(): ConnectorRoutesBuilder
    {
        return $this->routeBuilder;
    }

    /**
     * Set Event Dispatcher
     */
    protected function setRouteBuilder(ConnectorRoutesBuilder $routesBuilder): self
    {
        $this->routeBuilder = $routesBuilder;

        return $this;
    }
}
