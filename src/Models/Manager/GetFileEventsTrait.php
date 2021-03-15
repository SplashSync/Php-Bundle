<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Models\Manager;

use Splash\Bundle\Events\ObjectFileEvent;
use Splash\Core\SplashCore as Splash;
use Splash\Local\Local;

/**
 * Files Events Manager for Splash Connectors
 */
trait GetFileEventsTrait
{
    /**
     * Read File from Local Server with Md5 Protection
     *
     * @param ObjectFileEvent $event
     *
     * @return bool
     */
    public function onGetFileEvent(ObjectFileEvent $event)
    {
        //====================================================================//
        //  Verify if File Exists
        if (!Splash::file()->isFile($event->getPath(), $event->getMd5())) {
            return false;
        }

        //====================================================================//
        // PHPUNIT Exception => Look First in Local FileSystem
        //====================================================================//
        if (Splash::isDebugMode()) {
            //====================================================================//
            //  Read File Contents
            $fileArray = Splash::file()->getFile($event->getPath(), $event->getMd5());
            if ($fileArray) {
                //====================================================================//
                //  Push File Contents to Event
                $event->setContents($fileArray);

                return true;
            }
        }

        //====================================================================//
        //  Read File Contents
        $fileArray = Splash::file()->readFile($event->getPath(), $event->getMd5());
        if (!is_array($fileArray) || !isset($fileArray["raw"])) {
            return false;
        }
        //====================================================================//
        //  Push File Contents to Event
        $event->setContents($fileArray);

        return true;
    }
}
