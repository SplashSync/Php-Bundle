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

namespace Splash\Bundle\Models\Manager;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Symfony Cache Management for Splash Connectors Manager
 */
trait CacheTrait
{
    /**
     * Symfony Cache Pool
     */
    private CacheItemPoolInterface $cache;

    /**
     * Store Symfony Cache for Splash Connectors
     */
    protected function setCache(CacheItemPoolInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get Symfony Cache for Splash Connectors
     */
    protected function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * Check if Cache is Enabled for Splash Connectors
     */
    protected function isCacheEnabled(): bool
    {
        return !empty($this->configuration['cache']['enabled']);
    }

    /**
     * Get Cache Lifetime for Splash Connectors
     */
    protected function getCacheLifetime(): ?int
    {
        $lifetime = $this->configuration['cache']['lifetime'] ?? null;

        if (!is_scalar($lifetime) || empty($lifetime)) {
            return null;
        }

        return (int) $lifetime;
    }

    /**
     * Get Cache Key for Splash Connectors
     */
    protected function getCacheKey(string $key): string
    {
        return sprintf('splash.server.config.%s', $key);
    }
}
