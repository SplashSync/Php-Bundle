<?php

/**
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @author Bernard Paquier <contact@splashsync.com>
 **/


namespace Splash\Bundle\Interfaces\Connectors;

use ArrayObject;

/**
 * @abstract Define Required structure for Connectors Files Access 
 */
interface FileInterface {
    
    /**
     * @abstract   Read a file from Remote Server
     * 
     * @param   string      $Path           File Full Path on remote Server 
     * @param   string      $Md5            File MD5 Checksum 
     * 
     * @return  ArrayObject|bool
     */
    public function getFile(string $Path, string $Md5, bool $Queue = False);
    
}
