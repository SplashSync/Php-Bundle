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
 */

namespace Splash\Bundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
//use Sonata\Bundle\DemoBundle\Entity\Inspection;

//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


//use Nodes\CoreBundle\Entity\Node;

use Splash\Bundle\Admin\ObjectsModelManager;

/**
 */
class ProfileAdmin extends Admin
{
    
    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);
        
        $this->baseRouteName    = "sonata_admin_splash_" . $code;
        $this->baseRoutePattern = $code;
        
//        $this->setModelManager($manager);
        
    }    
    
//    public function setModelManager(ModelManagerInterface $modelManager)
//    {
////        $this->modelManager = new ObjectsModelManager($modelManager->getRegi);
//    }    
    
//    public function setBaseRouteName($baseRouteName)
//    {
//        $this->baseRouteName = $baseRouteName;
//    }
//    
//    protected    $baseRoutePattern = "dddd";
    
    
//    /**
//     * @var string
//     */
//    private $name;
//         
//    /**
//     * @var string
//     */
//    private $service;
    
//    /**
//     * @param string $name
//     * @param string $format
//     */
//    public function setConfiguration(string $name, string $format)
//    {
//        $this->name     =   $name;
//        $this->format   =   $format;
//    }     
    
    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
//            ->add('name')
//            ->add('IsActive')
//            ->add('createdAt')
//            ->add('status')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->addIdentifier('name')
//            ->add('user.username')
//            ->add('IsActive', null, array('editable' => true))
//            ->add('deleted', null, array('editable' => true))
//            ->add('host')
//            ->add('status')
//            ->add('http_auth')
//            ->add('connectorName')
//            ->add('updatedAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('user')
//            ->add('name')
//            ->add('host')
//            ->add('deleted')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
//                ->add('name')
//                ->add('IsActive')
//                ->add('deleted')
//                ->add('status', ChoiceType::class, array(
//                    "choices"   =>  Node::getChoices(),
//                    "choice_translation_domain"   => "NodesCoreBundle",
//                ))
//                ->add('connectorName', ChoiceType::class, array(
//                    "choices"                       =>  Node::getTypesChoices(),
//                    "choice_translation_domain"     =>  False,
//                ))
//                ->add('user', 'sonata_type_model_list')
//            ->end()
//            ->with('Webservice', array('class' => 'col-md-6'))
//                ->add('identifier')
//                ->add('host')
//                ->add('folder')
//                ->add('https', 'checkbox', array(
//                    'property_path'             => 'settings[EnableHttps]',
//                    'required'                  => False,
//                    ))
//            ->end()
//            ->with('Security', array('class' => 'col-md-6'))
//                ->add('http_auth')
//                ->add('http_user')
//                ->add('http_pwd')
//            ->end()                
//            ->with('Encoding', array('class' => 'col-md-6'))
//                ->add('crypt_mode')
//                ->add('crypt_key')
//            ->end()                
//            ->with('inspections', array('class' => 'col-md-12'))
//                ->add('inspections', 'sonata_type_collection', array(
//                    'by_reference'       => false,
//                    'cascade_validation' => true,
//                ), array(
//                    'edit' => 'inline',
//                    'inline' => 'table'
//                ))
            ->end()
        ;
    }
}
