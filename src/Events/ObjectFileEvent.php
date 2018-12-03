<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * @abstract    Request Server File Event
 * This Event is Triggered by Any Connector to Ask for File Conbtents on Server
 */
class ObjectFileEvent extends Event
{
    /**
     * Event Name.
     */
    const NAME = 'splash.connectors.file';

    /**
     * @abstract    WebService Id Of Impacted Server
     *
     * @var string
     */
    private $webserviceId;

    /**
     * @abstract    File Path Parameters
     *
     * @var string
     */
    private $path;

    /**
     * @abstract    File Md5 Checksum
     *
     * @var string
     */
    private $md5;

    /**
     * @abstract    File Contents Array
     *
     * @var array
     */
    private $contents;

    //==============================================================================
    //      EVENT CONSTRUCTOR
    //==============================================================================

    /**
     * @abstract    Event Constructor
     *
     * @param string $webserviceId
     * @param string $path
     * @param string $md5
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
     * @abstract    Get WebService Id
     *
     * @return string
     */
    public function getWebserviceId(): string
    {
        return $this->webserviceId;
    }

    /**
     * @abstract    Get File Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @abstract    Get File Md5
     *
     * @return string
     */
    public function getMd5(): string
    {
        return $this->md5;
    }

    /**
     * @abstract    Check if File Was Found
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return !empty($this->contents);
    }

    /**
     * @abstract    Set File Contents
     *
     * @param array $contents Splash File Contents
     *
     * @return self
     */
    public function setContents(array $contents)
    {
        $this->contents = $contents;

        return $this;
    }
    
    /**
     * @abstract    Get File Contents
     *
     * @return array|false
     */
    public function getContents()
    {
        if (!$this->isFound()) {
            return false;
        }

        return $this->contents;
    }
}
