<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Connectors;

use Psr\Log\LoggerInterface;

/**
 * @abstract    Manage Monolog Logger for Connectors
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @abstract    Get Event Dispatcher
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @abstract    Set Event Dispatcher
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    protected function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
