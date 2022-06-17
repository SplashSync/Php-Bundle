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

namespace Splash\Bundle\Events;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @abstract Request Server File Event
 * This Event is Triggered by Any Connector to Ask for File Conbtents on Server
 */
class ObjectFileEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'Splash\Bundle\Events\ObjectFileEvent';

    /**
     * WebService ID Of Impacted Server
     *
     * @var string
     */
    private string $webserviceId;

    /**
     * File Path Parameters
     *
     * @var string
     */
    private string $path;

    /**
     * File Md5 Checksum
     *
     * @var string
     */
    private string $md5;

    /**
     * File Contents Array
     *
     * @var null|array
     */
    private ?array $contents = null;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * Event Constructor
     *
     * @param string $webserviceId Webservice ID
     * @param string $path         File Path
     * @param string $md5          File Md5 Checksum
     */
    public function __construct(string  $webserviceId, string $path, string $md5)
    {
        //==============================================================================
        //      Data Storages
        $this->webserviceId = $webserviceId;
        $this->path = $path;
        $this->md5 = $md5;
    }

    //==============================================================================
    //      GETTERS & SETTERS
    //==============================================================================

    /**
     * Get WebService ID
     *
     * @return string
     */
    public function getWebserviceId(): string
    {
        return $this->webserviceId;
    }

    /**
     * Get File Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get File Md5
     *
     * @return string
     */
    public function getMd5(): string
    {
        return $this->md5;
    }

    /**
     * Check if File Was Found
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return !empty($this->contents);
    }

    /**
     * Set File Contents
     *
     * @param array $contents Splash File Contents
     *
     * @return self
     */
    public function setContents(array $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get File Contents
     *
     * @return null|array
     */
    public function getContents(): ?array
    {
        if (!$this->isFound()) {
            return null;
        }

        return $this->contents;
    }
}
