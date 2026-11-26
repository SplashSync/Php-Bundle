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

namespace Splash\Bundle\Tests\Connectors;

/**
 * Demo Connector: a mapped class that is not an AI Tool
 */
class InvalidAiToolsConnector extends MappedAiToolsConnector
{
    /**
     * AI Tools Classes: the Connector itself is not a Tool
     *
     * @var array<int, class-string>
     */
    protected static array $aiToolsMap = array(
        MappedAiToolsConnector::class,
    );
}
