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

namespace Splash\Bundle\Helpers\Objects;

use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Cache Helper for Objects Listing
 *
 * Allow Connectors to:
 * =>> Store Whole Objects List in Cache for a given period
 * =>> Filter Objects Varaiables with Splash Text Filter
 * =>> Retrieve Objects with Splash Paging Paremeters
 */
class CachedListHelper
{
    /**
     * Default Delay in Seconds Before Cache Refresh
     */
    const DEFAULT_DELAY = 60;

    /**
     * Symfony Cache Manager
     *
     * @var FilesystemCache
     */
    private $cache;

    /**
     * Connector WebService Id
     *
     * @var string
     */
    private $webserviceId;

    /**
     * Delay in Seconds Before Cache Refresh
     *
     * @var int
     */
    private $expireAfter;

    /**
     * Current Object Cache Key
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Current Cached Contents
     *
     * @var array
     */
    private $contents;

    /**
     * Current Filtered Contents Counter
     *
     * @var int
     */
    private $filteredCount = 0;

    /**
     * Class Constructor
     *
     * @param string $webserviceId
     * @param string $cackeKey
     * @param int    $expireAfter
     */
    public function __construct(string $webserviceId, string $cackeKey, int $expireAfter = self::DEFAULT_DELAY)
    {
        //====================================================================//
        // Store Config
        $this->webserviceId = $webserviceId;
        $this->cacheKey = $cackeKey;
        $this->expireAfter = $expireAfter;
        //====================================================================//
        // Init Symfony Cache
        $this->cache = new FilesystemCache();
        //====================================================================//
        // Load Cached Values
        if ($this->cache->has($this->getCacheKey())) {
            $this->contents = $this->cache->get($this->getCacheKey());
        }
    }

    /**
     * Check if Cache Contents are Available
     *
     * @return bool
     */
    public function hasCache(): bool
    {
        return isset($this->contents);
    }

    /**
     * Setup Content Cache
     *
     * @param array $contents
     *
     * @return $this
     */
    public function setContents(array $contents): self
    {
        //====================================================================//
        // Store Contents in Filesystem Cache
        $this->cache->set($this->getCacheKey(), $contents, $this->expireAfter);
        //====================================================================//
        // Store Contents in Class
        $this->contents = $contents;

        return $this;
    }

    /**
     * Retreive All Contents
     *
     * @return array
     */
    public function getContents(): array
    {
        //====================================================================//
        // No Cached Values
        if (!isset($this->contents)) {
            return array();
        }

        return $this->contents;
    }

    /**
     * Retreive Contents with Splash Paging
     *
     * @param null|string $filter
     * @param null|array  $parameters
     *
     * @return array
     */
    public function getPagedContents(string $filter = null, array $parameters = null): array
    {
        //====================================================================//
        // No Cached Values
        if (!isset($this->contents)) {
            $this->filteredCount = 0;

            return array();
        }
        //====================================================================//
        // Filter Contents
        $filtered = self::filterContents($this->contents, $filter);
        $this->filteredCount = count($filtered);
        //====================================================================//
        // Return Reduced List
        return self::reduceContents($filtered, $parameters);
    }

    /**
     * Get Total Number of Cached Items
     *
     * @return int
     */
    public function getTotal(): int
    {
        if ($this->hasCache()) {
            return count($this->contents);
        }

        return 0;
    }

    /**
     * Get Total Number of Cached Items
     *
     * @return int
     */
    public function getFilteredTotal(): int
    {
        return $this->filteredCount;
    }

    /**
     * Get Current Cache Key for Storage
     *
     * @return string
     */
    private function getCacheKey(): string
    {
        return $this->webserviceId.$this->cacheKey;
    }

    /**
     * Filter Contents with a String Search on all Datas
     *
     * @param array       $contents
     * @param null|string $filter
     *
     * @return array
     */
    private static function filterContents(array $contents, string $filter = null): array
    {
        //====================================================================//
        // No Filter
        if (empty($filter)) {
            return $contents;
        }
        //====================================================================//
        // Search for this Filter in all vars
        $results = array();
        foreach ($contents as $item) {
            //====================================================================//
            // Item is An Object
            if (is_object($item) && (false !== array_search($filter, get_object_vars($item), true))) {
                $results[] = $item;
            }
            //====================================================================//
            // Item is An Array
            if (is_array($item) && (false !== array_search($filter, $item, true))) {
                $results[] = $item;
            }
        }

        return $results;
    }

    /**
     * Reduce Contents List with Splash Paging
     *
     * @param array      $contents
     * @param null|array $parameters
     *
     * @return array
     */
    private static function reduceContents(array $contents, array $parameters = null): array
    {
        //====================================================================//
        // Cached With Parameters
        if (is_array($parameters) && isset($parameters["max"], $parameters["offset"])) {
            return array_slice($contents, $parameters["offset"], $parameters["max"]);
        }
        //====================================================================//
        // Cached Without Parameters
        return array_slice($contents, 0, 25);
    }
}
