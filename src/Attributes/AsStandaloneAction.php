<?php

declare(strict_types=1);

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

namespace Splash\Bundle\Attributes;

use Attribute;
use Splash\Bundle\Dictionary\StandaloneServiceTags;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Register a Service as a Splash Sync Standalone Webservice Action
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AsStandaloneAction extends Autoconfigure
{
    public function __construct(
        string $type,
        string $action,
        array $bind = array()
    ) {
        parent::__construct(
            tags: array(
                array(StandaloneServiceTags::ACTION => array('type' => $type, 'action' => $action)),
            ),
            bind: $bind,
        );
    }
}
