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

use Splash\Core\Helpers\ScopesHelper;

/**
 * Manage Scopes for Connectors
 */
trait ScopesAwareTrait
{
    /**
     * @var string[]
     */
    private array $taggedScopes = array();

    /**
     * Register a Tagged Standalone Scope
     *
     * @param string $scopeCodeOrClass
     */
    public function registerScope(string $scopeCodeOrClass): void
    {
        // For better Perf, Scopes are Parsed on Reading
        $this->taggedScopes[] = $scopeCodeOrClass;
    }

    /**
     * Get Tagged Standalone Scopes
     *
     * @return string[]
     */
    public function getRegisteredScopes(): array
    {
        $scopes = array();
        //====================================================================//
        // Walk on received Scopes Codes or Classes
        foreach ($this->taggedScopes as $taggedScope) {
            //====================================================================//
            // Identify Scope Class
            if ($scope = ScopesHelper::resolve($taggedScope)) {
                $scopes[$scope::getCode()] = $scope::getCode();
            }
        }

        return $scopes;
    }
}
