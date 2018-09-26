<?php

namespace Splash\Bundle\DependencyInjection;

//==============================================================================
// Framework Namespaces
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Splash\Bundle\Admin\ObjectAdmin;

//use Sonata\AdminBundle\Controller\CRUDController;
use Splash\Bundle\Admin\ObjectCRUDController as CRUDController;

use Splash\Connectors\FakerBundle\Entity\FakeObject;

use Splash\Bundle\Admin\ObjectsModelManager;

/**
 * @abstract    This is the class that loads and manages Splash bundle configuration
 *
 * @author Bernard Paquier <contact@splashsync.com>
 */
class SplashExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $container->setParameter('splash', $config);
        
        //====================================================================//
        // Add Availables Connections to Sonata Admin	
        foreach ($config["connections"]  as $Id => $Connection) {
            //====================================================================//
            // Objects Sonata Admin Class	
            $container
                ->register('splash.admin.' . $Id, ObjectAdmin::class)
                    ->addTag("sonata.admin", array( 
                        "manager_type"  => "orm", 
//                        "manager_type"  => "splash.objects", // "orm", 
                        "group"         => $Connection["name"], 
                        "label"         => "Objects", 
                        "icon"          => '<span class="fa fa-binoculars"></span>' 
                    ))
                    ->setArguments(array(
                        'splash.admin.' . $Id,
                        FakeObject::class,
                        CRUDController::class,
//                        $container->get("splash.connectors.modelmanager")
//                        ObjectsModelManager::class
//                        "@splash.connectors.modelmanager"
                        
                        ))
//                    ->addMethodCall("setModelManager", array(ObjectsModelManager::class))
                    ;
            //====================================================================//
            // Widgets Sonata Admin Class	
            
        }             
    }
}
