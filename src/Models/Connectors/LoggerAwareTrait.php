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

use Psr\Log\LoggerInterface;

/**
 * Manage Monolog Logger for Connectors
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Get Logger
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set Logger
     */
    protected function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }
}
