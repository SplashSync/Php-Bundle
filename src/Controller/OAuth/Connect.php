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

namespace Splash\Bundle\Controller\OAuth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * TEST ONLY - Try Authentification of a User from Connector
 */
class Connect extends AbstractController
{
    /**
     * Check if User is Logged In
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        //==============================================================================
        // Verify User is Logged In
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        return new RedirectResponse("/");
    }
}
