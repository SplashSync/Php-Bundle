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

namespace Splash\Bundle\Models\Connectors\Standalone;

use Exception;
use Splash\Bundle\Models\AbstractStandaloneWidget;

/**
 * Widgets of the Standalone Connector: served by tagged services
 */
trait WidgetsTrait
{
    /**
     * List of Tagged Widgets Services
     *
     * @var array<string, AbstractStandaloneWidget>
     */
    private array $taggedWidgets = array();

    /**
     * {@inheritdoc}
     */
    public function getAvailableWidgets(): array
    {
        return array_keys($this->taggedWidgets);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getWidgetDescription(string $widgetType): array
    {
        return $this->getWidgetService($widgetType)->description();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getWidgetContents(string $widgetType, array $params = array()): ?array
    {
        return $this->getWidgetService($widgetType)->get($params);
    }

    /**
     * Register a Tagged Standalone Widget Service
     *
     * @param string                   $widgetType
     * @param AbstractStandaloneWidget $widgetService
     *
     * @return void
     */
    public function registerWidgetService(string $widgetType, AbstractStandaloneWidget $widgetService): void
    {
        $this->taggedWidgets[$widgetType] = $widgetService;
    }

    /**
     * Get Configured to Standalone Object Service
     *
     * @param string $widgetType
     *
     * @throws Exception
     *
     * @return AbstractStandaloneWidget
     */
    public function getWidgetService(string $widgetType): AbstractStandaloneWidget
    {
        //====================================================================//
        // Safety Check
        if (!isset($this->taggedWidgets[$widgetType])) {
            throw new Exception(sprintf(
                'Standalone Widget  "%s" is not defined. Did you tag your service as  "splash.standalone.widget"?',
                $widgetType
            ));
        }
        //====================================================================//
        // Configure Widget Service
        $this->taggedWidgets[$widgetType]->configure($widgetType, $this->getWebserviceId(), $this->getConfiguration());

        //====================================================================//
        // Connect to Widget Service
        return $this->taggedWidgets[$widgetType];
    }
}
