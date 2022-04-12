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

namespace Splash\Bundle\Interfaces\Connectors;

/**
 * Define Required structure for Connectors Files Access
 */
interface FilesInterface
{
    /**
     * Read a file from Remote Server
     *
     * @param string $filePath File Full Path on remote Server
     * @param string $fileMd5  File MD5 Checksum
     *
     * @return null|array
     */
    public function getFile(string $filePath, string $fileMd5): ?array;
}
