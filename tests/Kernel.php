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

// phpcs:disable PSR1.Classes.ClassDeclaration

namespace Splash\Bundle\Tests;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Check if Php Bundle is installed, if so, use Symfony Web Test Case as base Test Case
 */
if (method_exists(MicroKernelTrait::class, "registerBundles")) {
    /**
     * Symfony Test Kernel
     */
    class Kernel extends BaseKernel
    {
        use MicroKernelTrait;

        /**
         * Gets the path to the configuration directory.
         */
        protected function getConfigDir(): string
        {
            return $this->getProjectDir().'/tests/config';
        }
    }
} else {
    /**
     * Symfony Test Kernel
     */
    class Kernel extends BaseKernel
    {
        use MicroKernelTrait;

        /**
         * Register App Bundles
         *
         * @retrun void
         */
        public function registerBundles(): iterable
        {
            //==============================================================================
            // Search for All Active Bundles
            $bundles = require $this->getConfigDir()."/bundles.php";

            foreach ($bundles as $class => $envs) {
                if (isset($envs['all']) || isset($envs[$this->environment])) {
                    /** @phpstan-ignore-next-line  */
                    yield new $class();
                }
            }
        }

        /**
         * Configure Container from Project Config Dir & Toolkit Config Dir
         *
         * @param ContainerBuilder $container
         * @param LoaderInterface  $loader
         *
         * @throws Exception
         *
         * @return void
         *
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         */
        protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
        {
            $confDir = $this->getConfigDir();

            $loader->load($confDir.'/{packages}/*.yaml', 'glob');
            $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*.yaml', 'glob');
            $loader->load($confDir.'/{services}.yaml', 'glob');
            $loader->load($confDir.'/{services}_'.$this->environment.'.yaml', 'glob');
        }

        /**
         * Configure Sf Routing
         *
         * @param RouteCollectionBuilder $routes
         *
         * @throws LoaderLoadException
         */
        protected function configureRoutes(RouteCollectionBuilder $routes): void
        {
            $confDir = $this->getConfigDir();

            $routes->import($confDir.'/{routes}/*.yaml', '/', 'glob');
            $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*.yaml', '/', 'glob');
            $routes->import($confDir.'/{routes}.yaml', '/', 'glob');
        }

        /**
         * Gets the path to the configuration directory.
         */
        protected function getConfigDir(): string
        {
            return $this->getProjectDir().'/tests/config';
        }
    }
}
