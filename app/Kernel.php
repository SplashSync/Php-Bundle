<?php

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends BaseKernel
{
    public function registerBundles()
    {
            
        //==============================================================================
        // SYMFONY CORE
        $bundles[] = new Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        $bundles[] = new Symfony\Bundle\SecurityBundle\SecurityBundle();
        $bundles[] = new Symfony\Bundle\TwigBundle\TwigBundle();
//        $bundles[] = new Symfony\Bundle\AsseticBundle\AsseticBundle();
        $bundles[] = new Symfony\Bundle\MonologBundle\MonologBundle();
        
        //==============================================================================
        // DOCTRINE CORE
        $bundles[] = new Doctrine\Bundle\DoctrineBundle\DoctrineBundle();

//        //==============================================================================
//        // FOS JS ROUTING
//        $bundles[] = new FOS\JsRoutingBundle\FOSJsRoutingBundle();
//
//        //==============================================================================
//        // KNP TIME
//        $bundles[] = new Knp\Bundle\TimeBundle\KnpTimeBundle();
//
//        //==============================================================================
//        // KNP MENU
//        $bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();
//
//        //==============================================================================
//        // SONATA CORE
//        $bundles[] = new Sonata\CoreBundle\SonataCoreBundle();
//
//        //==============================================================================
//        // SONATA BLOCKS
//        $bundles[] = new Sonata\BlockBundle\SonataBlockBundle();

//        //==============================================================================
//        // MOPA BOOTSTRAP
//        $bundles[] = new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle();
        
        //==============================================================================
        // SPLASH PHP BUNDLE
        $bundles[] = new Splash\Bundle\SplashBundle();

        //==============================================================================
        // SPLASH CONNECTORS BUNDLE
        $bundles[] = new Splash\Connectors\FakerBundle\SplashFakerBundle();
        
        //==============================================================================
        // TEST & DEV BUNDLES
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            }
            
            if (('dev' === $this->getEnvironment()) && class_exists("\Symfony\Bundle\WebServerBundle\WebServerBundle")) {
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if ("test" == $this->getEnvironment()) {
            $loader->load($this->getRootDir().'/config_test.yml');
            return;
        }
        $loader->load($this->getRootDir().'/config.yml');
    }
}
