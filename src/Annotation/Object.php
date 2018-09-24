<?php

namespace Splash\Bundle\Annotation;

use Splash\Core\SplashCore as Splash;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Object
{
    const Default_Transformer = "Splash.Objects.Transformer";

    /**
     * @var string
     */
    private $class;
    
    /**
     * @abstract    Doctrine Entity or Document Manager
     * @var mixed
     */
    private $manager;

    /**
     * @abstract    Class of Object Document
     * @var string
     */
    public $target;
    
    /**
     * @var string
     * @Required
     */
    public $type;
    
    /** @var boolean */
    public $disabled        = false;
    
    /**
     * @var string
     * @Required
     */
    public $name;
    
    /** @var string */
    public $description     = 'No Object Description Given';

    /** @var string */
    public $icon            = "fa fa-cubes";
    
    /**
     *  Object Synchronization Limitations
     *
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    
    /** @var boolean */
    public $allow_push_created     =   true;        // Allow Creation Of New Local Objects
    /** @var boolean */
    public $allow_push_updated     =   true;        // Allow Update Of Existing Local Objects
    /** @var boolean */
    public $allow_push_deleted     =   true;        // Allow Delete Of Existing Local Objects
    
    /**
     *  Object Synchronization Recommended Configuration
     */
    /** @var boolean */
    public $enable_push_created    =   true;         // Enable Creation Of New Local Objects when Not Existing
    /** @var boolean */
    public $enable_push_updated    =   true;         // Enable Update Of Existing Local Objects when Modified Remotly
    /** @var boolean */
    public $enable_push_deleted    =   true;         // Enable Delete Of Existing Local Objects when Deleted Remotly

    /** @var boolean */
    public $enable_pull_created    =   true;         // Enable Import Of New Local Objects
    /** @var boolean */
    public $enable_pull_updated    =   true;         // Enable Import of Updates of Local Objects when Modified Localy
    /** @var boolean */
    public $enable_pull_deleted    =   true;         // Enable Delete Of Remotes Objects when Deleted Localy

    /**
     * @abstract    Service used instead of Doctrine Generic Repository
     * @var string
     */
    public $repository_service      =   null;
    
    /**
     * @abstract    Class used for Field Conversion to Splash Formats
     * @var string
     */
    public $transformer_service     =   self::Default_Transformer;
    
    public function setClass($class)
    {
        $this->class = $class;
        //====================================================================//
        // If no Target Class defined, use Splash Entity Class
        if (is_null($this->target)) {
            $this->target = $class;
        }
        return $this;
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function getTargetClass()
    {
        return $this->target;
    }
    
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }
    
    public function getManager()
    {
        return $this->manager;
    }

    public function getTransformerService()
    {
        return $this->transformer_service;
    }

    public function getRepositoryService()
    {
        return $this->repository_service;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function getDisabled()
    {
        return $this->disabled;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     *  @abstract   Override Get Description Array for requested Object Type
     *
     *  @return     array
     */
    public function getObjectDescription()
    {
        //====================================================================//
        // Build & Return Object Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Object Type Name
            "name"          =>  $this->getName(),                   // Object Display Neme
            "description"   =>  $this->getDescription(),            // Object Descritioon
            "icon"          =>  $this->getIcon(),                   // Object Icon
            "disabled"      =>  $this->getDisabled(),               // Is This Object Enabled or Not?
            //====================================================================//
            // Object Limitations
            "allow_push_created"      =>    $this->allow_push_created,
            "allow_push_updated"      =>    $this->allow_push_updated,
            "allow_push_deleted"      =>    $this->allow_push_deleted,
            //====================================================================//
            // Object Default Configuration
            "enable_push_created"     =>    $this->enable_push_created,
            "enable_push_updated"     =>    $this->enable_push_updated,
            "enable_push_deleted"     =>    $this->enable_push_deleted,
            "enable_pull_created"     =>    $this->enable_pull_created,
            "enable_pull_updated"     =>    $this->enable_pull_updated,
            "enable_pull_deleted"     =>    $this->enable_pull_deleted
        );
    }
}
