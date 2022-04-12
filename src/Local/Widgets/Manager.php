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

namespace Splash\Local\Widgets;

use ArrayObject;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Models\Widgets\WidgetInterface;

/**
 * Splash Bundle Connectors Widgets Access
 */
class Manager implements WidgetInterface
{
    /**
     * @var AbstractConnector
     */
    private AbstractConnector $connector;

    /**
     * @var string
     */
    private string $widgetType;

    //====================================================================//
    // Class Constructor
    //====================================================================//

    /**
     * Init a New Widget Manager
     *
     * @param AbstractConnector $connector
     * @param string            $widgetType
     */
    public function __construct(AbstractConnector $connector, string $widgetType)
    {
        $this->connector = $connector;
        $this->widgetType = $widgetType;
    }

    //====================================================================//
    //  COMMON CLASS INFORMATIONS
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public static function isDisabled(): bool
    {
        return false;
    }

    //====================================================================//
    // Class Main Functions
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function description(): array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getWidgetDescription($this->widgetType);
    }

    /**
     * {@inheritdoc}
     */
    public function get($parameters = array()): ?array
    {
        //====================================================================//
        // Forward Action
        return $this->connector->getWidgetContents($this->widgetType, self::toArray($parameters));
    }

    //====================================================================//
    // Tooling Functions
    //====================================================================//

    /**
     * Normalize Array or ArrayObject to Array
     *
     * @param null|array|ArrayObject $data
     *
     * @return array
     */
    private static function toArray($data) : array
    {
        if (($data instanceof ArrayObject)) {
            return $data->getArrayCopy();
        }
        if (is_null($data) || empty($data)) {
            return array();
        }

        return $data;
    }
}
